<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Serviço de leitura de planilhas Excel (.xlsx) e CSV.
 * Implementação nativa sem dependências externas.
 * Suporta .xlsx (Office Open XML) e .csv/.txt.
 */
class ExcelReaderService
{
    /**
     * Lê um arquivo de planilha e retorna os dados como array.
     * Detecta formato pelo extensão.
     */
    public function read(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Arquivo não encontrado: {$filePath}");
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'xlsx' => $this->readXlsx($filePath),
            'xls' => $this->readXlsx($filePath), // Tentativa, pode falhar para .xls antigo
            'csv', 'txt' => $this->readCsv($filePath),
            default => throw new \RuntimeException("Formato não suportado: .{$extension}. Use .xlsx ou .csv"),
        };
    }

    /**
     * Lê arquivo .xlsx (Office Open XML).
     * Extrai dados da primeira sheet.
     */
    private function readXlsx(string $filePath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \RuntimeException("Não foi possível abrir o arquivo Excel.");
        }

        // Ler shared strings (textos compartilhados)
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    // Suporta texto simples e rich text
                    if ($si->t) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif ($si->r) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // Ler primeira sheet
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXml) {
            $zip->close();
            throw new \RuntimeException("Planilha não encontrada no arquivo Excel.");
        }

        $xml = simplexml_load_string($sheetXml);
        if (!$xml) {
            $zip->close();
            throw new \RuntimeException("Erro ao processar dados da planilha.");
        }

        $rows = [];
        if (isset($xml->sheetData->row)) {
            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                $maxCol = 0;

                foreach ($row->c as $cell) {
                    $colIndex = $this->columnLetterToIndex((string) $cell['r']);
                    $maxCol = max($maxCol, $colIndex);

                    $value = '';
                    $type = (string) ($cell['t'] ?? '');

                    if ($type === 's') {
                        // Shared string
                        $index = (int) (string) $cell->v;
                        $value = $sharedStrings[$index] ?? '';
                    } elseif ($type === 'inlineStr') {
                        $value = (string) ($cell->is->t ?? '');
                    } else {
                        $value = (string) ($cell->v ?? '');
                    }

                    // Preencher células vazias intermediárias
                    while (count($rowData) < $colIndex) {
                        $rowData[] = '';
                    }
                    $rowData[$colIndex] = $value;
                }

                if (!empty(array_filter($rowData, fn($v) => $v !== ''))) {
                    $rows[] = $rowData;
                }
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Lê arquivo CSV/TXT.
     */
    private function readCsv(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Não foi possível abrir o arquivo CSV.");
        }

        // Detectar separador (vírgula, ponto-e-vírgula, tab)
        $firstLine = fgets($handle);
        rewind($handle);

        $separator = ',';
        if ($firstLine) {
            $semicolonCount = substr_count($firstLine, ';');
            $commaCount = substr_count($firstLine, ',');
            $tabCount = substr_count($firstLine, "\t");

            if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
                $separator = ';';
            } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
                $separator = "\t";
            }
        }

        while (($data = fgetcsv($handle, 0, $separator)) !== false) {
            if (!empty(array_filter($data, fn($v) => $v !== '' && $v !== null))) {
                $rows[] = $data;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Converte referência de célula (ex: "B3") em índice de coluna (0-based).
     */
    private function columnLetterToIndex(string $cellRef): int
    {
        // Extrair apenas letras da referência
        $letters = preg_replace('/[^A-Z]/i', '', $cellRef);
        $letters = strtoupper($letters);

        $index = 0;
        $length = strlen($letters);
        for ($i = 0; $i < $length; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return $index - 1; // 0-based
    }

    /**
     * Interpreta dados brutos da planilha como horários por hotel.
     * 
     * Formatos suportados:
     * 
     * Formato 1 (colunas): Hotel | Horário 1 | Horário 2 | ...
     *   Barceló    | 07:20 | 08:10
     *   Hard Rock  | 06:50 |
     * 
     * Formato 2 (duas colunas): Hotel | Horário
     *   Barceló    | 07:20
     *   Barceló    | 08:10
     *   Hard Rock  | 06:50
     * 
     * @return array [['hotel' => 'Nome', 'times' => ['07:20', '08:10']], ...]
     */
    public function parseHotelSchedules(array $rawData): array
    {
        if (empty($rawData)) {
            return [];
        }

        // Detectar se a primeira linha é cabeçalho
        $firstRow = $rawData[0] ?? [];
        $hasHeader = $this->isHeaderRow($firstRow);
        $startRow = $hasHeader ? 1 : 0;

        // Detectar formato baseado na estrutura dos dados
        $result = [];
        $hotelGroups = [];

        for ($i = $startRow; $i < count($rawData); $i++) {
            $row = $rawData[$i];
            if (empty($row)) continue;

            $hotelName = trim($row[0] ?? '');
            if (empty($hotelName)) continue;

            // Coletar horários das colunas subsequentes
            $times = [];
            for ($col = 1; $col < count($row); $col++) {
                $value = trim($row[$col] ?? '');
                if ($this->isTimeValue($value)) {
                    $times[] = $this->normalizeTime($value);
                }
            }

            // Se é formato 2 (uma linha por horário), agrupa pelo nome do hotel
            if (!isset($hotelGroups[$hotelName])) {
                $hotelGroups[$hotelName] = [];
            }
            $hotelGroups[$hotelName] = array_merge($hotelGroups[$hotelName], $times);
        }

        // Montar resultado final
        foreach ($hotelGroups as $hotel => $times) {
            $uniqueTimes = array_unique($times);
            sort($uniqueTimes);
            $result[] = [
                'hotel' => $hotel,
                'times' => array_values($uniqueTimes),
            ];
        }

        return $result;
    }

    /**
     * Verifica se a linha parece ser um cabeçalho.
     */
    private function isHeaderRow(array $row): bool
    {
        $firstCell = strtolower(trim($row[0] ?? ''));
        $headerKeywords = ['hotel', 'nombre', 'nome', 'name', 'resort'];
        
        foreach ($headerKeywords as $keyword) {
            if (str_contains($firstCell, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se um valor parece ser um horário.
     */
    private function isTimeValue(string $value): bool
    {
        if (empty($value)) return false;

        // Formato HH:MM ou H:MM
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            return true;
        }

        // Número decimal (Excel armazena horários como frações do dia)
        // Ex: 0.3055 = 07:20
        if (is_numeric($value) && (float) $value >= 0 && (float) $value < 1) {
            return true;
        }

        return false;
    }

    /**
     * Normaliza valor de horário para formato HH:MM.
     */
    private function normalizeTime(string $value): string
    {
        // Se já é formato HH:MM
        if (preg_match('/^(\d{1,2}):(\d{2})(:\d{2})?$/', $value, $matches)) {
            return sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        // Se é fração decimal (formato Excel)
        if (is_numeric($value)) {
            $totalMinutes = round((float) $value * 24 * 60);
            $hours = (int) floor($totalMinutes / 60);
            $minutes = (int) ($totalMinutes % 60);
            return sprintf('%02d:%02d', $hours, $minutes);
        }

        return $value;
    }
}
