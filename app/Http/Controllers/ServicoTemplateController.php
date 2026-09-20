<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServicoTemplateController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $cabecalhos = [
            'Nome', 'CÃ³digo', 'Categoria', 'DescriÃ§Ã£o', 'PreÃ§o', 'Status',
        ];

        $estiloCabecalho = new Style(
            fontBold: true,
            backgroundColor: Color::rgb(14, 116, 144),
            fontColor: Color::rgb(255, 255, 255),
        );

        $arquivo = tempnam(sys_get_temp_dir(), 'modelo_').'.xlsx';

        $writer = new Writer;
        $writer->openToFile($arquivo);
        $writer->addRow(Row::fromValuesWithStyle($cabecalhos, $estiloCabecalho));

        $writer->addRow(Row::fromValues([
            'InstalaÃ§Ã£o de Fibra',
            'SRV-0001',
            'InstalaÃ§Ã£o',
            'InstalaÃ§Ã£o de fibra Ã³ptica residencial',
            '199.90',
            'Ativo',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_servicos.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
