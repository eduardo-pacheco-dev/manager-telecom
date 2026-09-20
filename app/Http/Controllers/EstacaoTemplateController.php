<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class EstacaoTemplateController extends Controller
{
    public function __invoke()
    {
        $cabecalhos = [
            'Site_ID', 'Endereco_ID', 'Tipo_Elemento', 'Tecnologia', 'Classificacao',
            'Municipio', 'Estado', 'Regional', 'Status', 'Data_Aquisicao',
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
            'AC1001',
            'AC1001_001',
            'BTS',
            'LTE',
            'ACESSO',
            'São Paulo',
            'SP',
            'TCO',
            'Ativo',
            '2023-05-10',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_estacoes.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
