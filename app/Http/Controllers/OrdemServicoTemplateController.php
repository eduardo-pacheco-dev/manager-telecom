<?php

namespace App\Http\Controllers;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrdemServicoTemplateController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        $cabecalhos = [
            'Cód_AFL', 'Código_Personalizado', 'Código_Cliente', 'Cliente', 'Ordem_Complexa',
            'Status_Geral', 'Site_ID A', 'END_ID A', 'Site_ID B', 'END_ID B',
            'Projeto', 'Descrição', 'Supervisor', 'Coordenador', 'OC (TIM)', 'Chave_MW',
            'SMP_Nokia', 'OBS GERAL', 'Data_Cadastro_Ativ',
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
            'AFL20260620',
            'PERS-001',
            'CLI-500',
            'Cliente Exemplo',
            'COMPLEX-1',
            'Pendente OS/PO',
            '4G-JQIT19',
            'ACABL_0001',
            '4G-JQIT20',
            'ACABL_0002',
            'CT_REUSO',
            'VISTORIA TÉCNICA NO SITE',
            'Fabricio Paes',
            'Djalma Teixeira',
            '1311997',
            '',
            '',
            'Observação geral da ordem',
            '2026-05-05 00:00:00',
        ]));

        $writer->close();

        return response()
            ->download($arquivo, 'modelo_ordens_servico.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }
}
