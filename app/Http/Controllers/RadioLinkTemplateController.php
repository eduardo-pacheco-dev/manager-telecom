<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RadioLinkTemplateController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $cabecalhos = [
            'codigo',
            'nome',
            'estacao_a',
            'estacao_b',
            'frequencia',
            'capacidade',
            'canal',
            'polarizacao',
            'fabricante',
            'modelo',
            'distancia',
            'status',
            'data_ativacao',
            'observacao',
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
            'RL-EX0001',
            'Backhaul Centro',
            'AC10J1',
            'AC1001',
            23.000,
            '1 Gbps',
            '1E1',
            'Dupla',
            'ERICSSON',
            'MINI-LINK 6363',
            12.5,
            'Ativo',
            '2023-05-10',
            'Exemplo de linha - remova antes de importar.',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_radio_links.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
