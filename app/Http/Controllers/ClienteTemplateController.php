<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClienteTemplateController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $cabecalhos = [
            'Nome', 'Email', 'CNPJ', 'Telefone', 'Segmento', 'Cidade', 'Estado', 'Status',
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
            'TechNova Telecom',
            'contato@technova.com.br',
            '12.345.678/0001-95',
            '(11) 4002-8922',
            'Corporativo',
            'SÃ£o Paulo',
            'SP',
            'Ativo',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_clientes.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
