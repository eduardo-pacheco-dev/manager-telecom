<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ServicoTemplateController extends Controller
{
    public function __invoke()
    {
        $cabecalhos = [
            'Nome', 'Código', 'Categoria', 'Descrição', 'Preço', 'Status',
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
            'Instalação de Fibra',
            'SRV-0001',
            'Instalação',
            'Instalação de fibra óptica residencial',
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
