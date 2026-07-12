<?php
namespace App\Core;

class AzureStorage
{
    private static function getEnvVariables()
    {
        $env = [];
        // 1) .env del propio proyecto (prioridad)
        $localEnv  = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
        // 2) .env compartido en WS-ROOMS/ (fallback para claves que falten)
        $sharedEnv = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . '.env';

        foreach ([$localEnv, $sharedEnv] as $envFile) {
            if (!file_exists($envFile)) continue;
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    if ($name === '') continue;
                    if (!isset($env[$name])) {
                        $env[$name] = trim($value, '"\'');
                    }
                }
            }
        }
        
        return $env;
    }

    /**
     * Sube un archivo a Azure Blob Storage.
     * 
     * @param string $localFilePath Ruta temporal del archivo (ej. $_FILES['imagen']['tmp_name'])
     * @param string $fileName Nombre destino del archivo (ej. foto_123.jpg)
     * @param string $contentType Tipo MIME del archivo (ej. image/jpeg)
     * @return string|false Devuelve la URL final si es exitoso, o false si falla.
     */
    public static function uploadFile($localFilePath, $fileName, $contentType)
    {
        $env = self::getEnvVariables();
        $baseUrl = $env['AZURE_BLOB_BASE_URL'] ?? '';
        $sasToken = $env['AZURE_BLOB_SAS_TOKEN'] ?? '';

        if (empty($baseUrl) || empty($sasToken)) {
            return false;
        }

        // Si el fileName contiene carpetas (ej. usuarios/foto.jpg), codificamos cada parte
        // por separado para no codificar el slash (/) como %2F
        $parts = explode('/', $fileName);
        $encodedParts = array_map('rawurlencode', $parts);
        $encodedFileName = implode('/', $encodedParts);

        // Construir la URL completa con el token SAS
        $uploadUrl = $baseUrl . '/' . $encodedFileName . $sasToken;

        // Leer el contenido del archivo
        $fileContent = file_get_contents($localFilePath);
        if ($fileContent === false) {
            return false;
        }

        // Configurar opciones para el stream context HTTP
        $options = [
            'http' => [
                'method' => 'PUT',
                'header' => [
                    "x-ms-blob-type: BlockBlob",
                    "Content-Type: $contentType",
                    "Content-Length: " . strlen($fileContent)
                ],
                'content' => $fileContent,
                'ignore_errors' => true // Para poder capturar el código de respuesta incluso si falla
            ],
            // Desactivar validación SSL en local si fuera necesario
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];

        $context = stream_context_create($options);

        // Hacer la petición
        $result = file_get_contents($uploadUrl, false, $context);

        // Obtener los headers de respuesta para verificar el status code
        $statusCode = 0;
        
        $headers = [];
        if (function_exists('http_get_last_response_headers')) {
            $headers = http_get_last_response_headers();
        } elseif (isset($http_response_header)) {
            $headers = @$http_response_header;
        }

        if (!empty($headers) && is_array($headers)) {
            // El primer elemento suele ser algo como "HTTP/1.1 201 Created"
            if (preg_match('#^HTTP/\d(?:\.\d)?\s+(\d{3})#', $headers[0], $matches)) {
                $statusCode = intval($matches[1]);
            }
        }

        // Si el código HTTP es 201 Created, fue exitoso.
        if ($statusCode === 201) {
            // Retornamos solo la URL del archivo (sin el token) para guardarla en BD
            return $baseUrl . '/' . $encodedFileName;
        }

        return false;
    }
}
