<?php
return [
    'erro_inesperado' => 'A aplicação encontrou um erro inesperado. Favor contactar o Administrador.',
    'contrarrazao' =>
        ['ja_cadastrada_para_recurso' => 'Já existe contrarrazão cadastrada para este recurso'],
    'profissional' => [
        'registro_nome_nao_encrontrado' => 'Registro ou Nome informado, não foi encontrado na Base de Dados do SICCAU!'
    ],
    'substituicao_impugnacao' => [
        'mesmo_profissional' => 'Caro(a) sr. (a), em uma substituição, não é permitido incluir o mesmo nome/registro do substituído!'
    ],
    'membro_chapa' => [
        'pendencias_indentificadas' => 'Pendências identificados no transcorrer do processo eleitoral',
        'exclusao_unico_responsavel' => 'Não há outro responsável pela chapa. Para realizar a exclusão/alteração deste membro, por favor nomeie outro responsável',
        'ja_aceitou_convite' => 'Prezado (a) arquiteto (a) e urbanista, você já aceitou um convite para participar de uma chapa eleitoral.',
    ],
    'denuncia' => [
        'julgamento' => [
            'recurso' => [
                'contrarrazao' => [
                    'nao_existe_recurso_contrarrazao' => 'Não existe recurso para contrarrazão.',
                ],
                'prazo_solicitacao_recurso_encerrou' => 'Prezado(a), o prazo para solicitar o recurso ou a reconsideração encerrou.'
            ],
            'assessor_ce_outra_uf' => 'O julgamento não pode ser inserido por assessor CE de outra UF.',
            'nao_possivel_retificar_julgamento' => 'Não foi possível retificar o julgamento da denúncia.',
            'ja_inserido_primeira_instancia' => 'Prezado(a), não é possível incluir porque já existe um julgamento.',
            'nao_existe_julgamento_para_retificar' => 'Não existe julgamento para a denúncia que possa ser retificado.',
        ],
        'parecer' => [
            'alegacao_final_ja_respondida' => 'Prezado(a), não é possível incluir o encaminhamento porque as alegações finais foram respondidas pelas partes.',
            'alegacao_final_nao_respondida' => 'Prezado(a) uma das partes envolvidas ainda não respondeu a alegação final.'
        ],
        'sem_permissao_de_acesso' => 'Prezado(a), você não tem permissão de acesso',
        'permissao_somente_membro_comissao' => 'Prezado(a), você não tem permissão de acesso a esta denúncia.'
    ],
    'eleicao' => [
        'periodo_fechado' => "O período de vigência da ELEIÇÃO está fechado!",
    ],
    'permissao' => [
        'permissao_somente_membro_comissao' => 'Prezado(a), somente membros de comissão na eleição vigente podem acessar este menu.',
        'sem_acesso_visualizar' => 'Caro(a) sr. (a), você não tem permissão de acesso para visualização da atividade selecionada!',
        'sem_acesso_menu_profissional' => 'Prezado (a) arquiteto (a) e urbanista, você não possui acesso a esse item de menu.',
        'visualizacao_responsaveis_chapa' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para Responsáveis pela Chapa!',
        'visualizacao_membros_responsaveis_chapa' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros responsáveis pela chapa!',
        'visualizacao_membro_comissao' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros da Comissão Eleitoral!',
        'visualizacao_responsaveis_chapa_convite_aceito' => 'Caro(a) sr.(a) a visualização da atividade selecionada só está disponivéis para responsáveis pela Chapa que aceitaram o convite de chapa',
    ],
    'julgamento_final' => [
        'sem_julgamento_membro_comissao_cen' => 'Caro(a) sr.(a), não existe julgamento final cadastrado para a eleição que o senhor é um conselheiro CEN/BR!',
        'sem_julgamento_membro_comissao_uf' => 'Caro(a) sr.(a), não existe julgamento Final cadastrado para a UF, a qual o senhor é um conselheiro CE/UF!',
        'sem_julgamento_responsavel_chapa' => 'Caro(a) sr.(a), não existe julgamento final cadastrado para a eleição que o senhor é um responsável pela chapa!',
        'ja_realizado' => 'Caro(a) sr. (a), um dos Assessores, já executou o cadastro do julgamento o qual o senhor está solicitando!',
        'aguardar_finalizar_julgamento' => 'Caro(a) sr.(a), aguarde o período de julgamento ser finalizado, para acessar as informações!',
        'julgamento_final_deferido' => 'Caro(a) sr. (a), o julgamento final se encontra deferido!',
    ],
    'recurso_julgamento_final' => [
        'ja_realizado' => 'Caro(a) sr. (a), um dos Responsável da Chapa, já executou o cadastro do Recurso do julgamento o qual o senhor(a) está solicitando!',
        'vigencia_fechada' => 'Caro(a) sr. (a), o período de vigência encontra-se fechado, para eleição a qual o senhor é um dos responsáveis!',
    ],
    'recurso_julgamento_segunda_instancia_substituicao' => [
        'ja_realizado' => 'Caro(a) sr. (a), um dos responsáveis pela chapa, já está executando uma solicitação de Recurso do pedido de substituição!',
    ],
    'substituicao_julgamento_final' => [
        'error_msg' => [
            'justificativa_obrigatorio' => 'Caro(a) sr. (a), o campo Justificativa é obrigatório.',
            'julgamento_final_nao_encontrado' => 'Julgamento Final não encontrado.',
            'arquivo_nao_encontrado' => 'Arquivo não encontrado.',
            'julgamento_final_deferido' => 'Caro(a) sr. (a), o julgamento final se encontra deferido!',
            'indicacoes_nao_encontradas' => 'Caro(a) sr. (a), não existe indicações para o julgamento final!',
            'vigencia_fechada' => 'Caro(a) sr. (a), o período de vigência encontra-se fechado, para eleição a qual o senhor é um dos responsáveis!',
            'substituicoes_existentes' => 'Caro(a) sr. (a), um dos Responsável da Chapa, já executou o cadastro do Pedido de Substituição o qual o senhor(a) está solicitando!',
            'nao_eh_responsavel' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros responsáveis pela chapa!',
            'membros_obrigatorios' => 'Caro(a) sr. (a), para realização de uma substituição, você deve informar o substituído e o substituto!',
        ],
        'success_msg' => [],
    ],
    'corpo_email' => [
        'has_definicao_email' => "Já existe definição de e-mail cadastrada para a atividade principal ':0' e atividade secundária ':1'. Para realizar a alteração é necessário remover a definição de  e-mail.",
    ],
    'calendario' =>
        [
            'processo_ordinario_ja_cadastrado' => 'Já existe uma eleição “Ordinária” cadastrada para o período informado. Favor informar um período onde, a data de início e a data fim não esteja compreendido no período da eleição cadastrada.',
            'processo_extraordinario_ja_cadastrado' => 'Já existe uma eleição “Extraordinária” cadastrada para o período e UF informados. Favor informar UF (s) distintas e período onde, a data de início e/ou a data fim não esteja compreendido no período da eleição.'
        ],
    'julgamento_segunda_instancia_recurso' => [
        'sem_julgamento_membro_comissao_cen' => 'Caro(a) sr.(a), não existe julgamento final cadastrado para a eleição que o senhor é um conselheiro CEN/BR!',
        'ja_realizado' => 'Caro(a) sr. (a), um dos Assessores, já executou o cadastro do julgamento o qual o senhor está solicitando!',
    ],
    'julgamento_segunda_instancia_substituicao' => [
        'sem_julgamento_membro_comissao_cen' => 'Caro(a) sr.(a), não existe julgamento final cadastrado para a eleição que o senhor é um conselheiro CEN/BR!',
        'ja_realizado' => 'Caro(a) sr. (a), um dos Assessores, já executou o cadastro do julgamento o qual o senhor está solicitando!',
    ],
    'julgamento_final_segunda_instancia' => [
        'ja_realizado_alteracao' => 'Caro(a) sr. (a), um dos Assessores, já executou a alteração do julgamento o qual o senhor está solicitando!',
    ],
    'impugnacao_resultado' => [
        'sem_pedidos_cadastrados' => 'Não há pedidos de impugnação de resultado cadastrado',
        'sem_permitido_apenas_responsaveis_chapa' => 'Caro(a) sr. (a), a visualização de pedidos de impugnação de resultado é permitido somente para membros ou responsáveis de chapa'
    ],
    'julgamento_alegaca_impug_resultado' => [
        'ja_realizado' => 'Caro(a) sr. (a), um dos Assessores, já executou o cadastro do julgamento o qual o senhor está solicitando!',
    ],
    'contrarrazao_impugnacao_resultado' => [
        'ja_realizado_cadastro_impugnante' => 'Caro(a) sr. (a), já foi realizado o cadastro da contrarrazão a qual está solicitando!',
        'ja_realizado_cadastro_impugnado' => 'Caro (a) sr. (a), um dos responsáveis pela chapa, já executou o cadastro de contrarrazão para o recurso a qual está solicitando!',
        'periodo_vigente_cadastro' => 'Não é possível cadastrar a contrarrazão, pois o período informado na atividade 6.5 não está vigente',
    ],
    'recurso_impugnacao_resultado' => [
        'periodo_fora_vigencia' => 'Não é possível cadastrar o recurso, pois o período informado na atividade 6.4 não está vigente',
        'permissao_impugnante' => 'Caro(a) sr. (a), a funcionalidade está disponível apenas para o impugnante do pedido!',
        'permissao_impugnado' => 'Caro(a) sr. (a), a funcionalidade está disponível apenas para responsáveis da chapa!',
        'possui_recurso_cadastrado' => 'Caro(a) sr. (a), já possui um recurso ou reconsideração cadastrada pela chapa no qual o sr. é responsável!'
    ],
    'julg_recurso_impug_resultado' => [
        'ja_realizado' => 'Caro(a) sr. (a), um dos Assessores, já executou o cadastro do julgamento o qual o senhor está solicitando!',
        'periodo_fora_vigencia' => 'Caro (a) sr. (a), não é possível cadastrar o julgamento em 2ª instancia, pois o período informado na atividade 6.6 não está vigente'
    ]
];
