<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TimProjetoTemplateController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $cabecalhos = [
            'Código', 'Descrição', 'Status', 'Cliente', 'Estação',
            'OC', 'OS FAM Entrega', 'OS FAM Instalação', 'OS FAM Panorâmica', 'OS FAM Desinstalação',
            'Data Início', 'Data Fim', 'Ativo', 'Criar OS',
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
            'Implantação RAN TIM',
            'Implantação de estações RAN para a operadora TIM.',
            'Planejamento',
            'Cliente Exemplo',
            '4G-JQIT19',
            'OC-2026-001',
            '123-456-789',
            '123-456-790',
            '123-456-791',
            '123-456-792',
            '2026-01-10',
            '2026-12-20',
            'Sim',
            'Sim',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_projetos_tim.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}