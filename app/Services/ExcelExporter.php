<?php

namespace App\Services;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Options as XlsxOptions;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExporter
{
    /**
     * Exporta dados para um arquivo Excel (.xlsx) como resposta de download.
     *
     * @param  string  $nomeArquivo  Nome do arquivo, ex.: 'colaboradores.xlsx'
     * @param  array<int, string>  $cabecalho  Linha de cabeçalho
     * @param  array<int, array<int, mixed>>  $linhas  Linhas de dados
     */
    public function download(string $nomeArquivo, array $cabecalho, array $linhas): StreamedResponse
    {
        return response()->streamDownload(function () use ($cabecalho, $linhas): void {
            $writer = new XlsxWriter(new XlsxOptions);
            $writer->openToBrowser('php://output');

            $writer->addRow(Row::fromValues($cabecalho));

            foreach ($linhas as $linha) {
                $writer->addRow(Row::fromValues($linha));
            }

            $writer->close();
        }, $nomeArquivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
