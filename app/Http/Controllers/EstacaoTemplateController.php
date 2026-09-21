<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EstacaoTemplateController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $cabecalhos = [
            'Site_ID', 'Endereco_ID', 'Tecnologia', 'Tipo_Conexao', 'Station_ID',
            'Municipio', 'Estado', 'CEP', 'Regional', 'Status', 'Detentor_Area',
            'Tipo_Infra', 'Tipo_EV', 'Latitude', 'Longitude', 'Tipo_Logradouro',
            'Logradouro', 'Numero', 'Complemento', 'Bairro', 'Tipo_Torre',
            'AEV_Nominal', 'Area_Solo', 'Altura_Estrutura',
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
            'LTE',
            'Fibra Óptica',
            '68010010',
            'São Paulo',
            'SP',
            '01310-100',
            'TCO',
            'Ativo',
            'IHS BRAZIL',
            'Greenfield',
            'POSTE',
            '-10.925094',
            '-69.554056',
            'RUA',
            'Rua Augusta',
            '1234',
            'Apto 5',
            'Centro',
            'Torre 1',
            '0',
            '0',
            '40',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_estacoes.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
