<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ColaboradorTemplateController extends Controller
{
    public function __invoke()
    {
        $cabecalhos = [
            'Nome', 'Email', 'CPF', 'Telefone', 'Cargo', 'Departamento',
            'Categoria', 'Data_Admissão', 'Salário', 'Status',
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
            'João da Silva',
            'joao.silva@exemplo.com',
            '123.456.789-01',
            '(11) 99999-0000',
            'Analista de Rede',
            'TI',
            'CLT',
            '2024-01-15',
            '8500.00',
            'Ativo',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_colaboradores.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
