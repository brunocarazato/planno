import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    ClipboardList,
    Clock3,
    FolderKanban,
    School,
    Target,
    UserCheck,
    UsersRound,
} from 'lucide-react';
import type { ComponentType } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';

type MetricasDashboard = {
    projetos: {
        total: number;
        emIniciacao: number;
    };
    turmas: {
        total: number;
        ativas: number;
        aceitandoCadastros: number;
    };
    alunos: {
        cadastros: number;
        pendentes: number;
        aprovadosAtivos: number;
    };
};

type ProfessorDashboardProps = {
    metricas: MetricasDashboard;
};

type Indicador = {
    rotulo: string;
    valor: number;
    apoio: string;
    icon: ComponentType<{ className?: string }>;
    tom: string;
};

export default function ProfessorDashboard({ metricas }: ProfessorDashboardProps) {
    const grupos = [
        {
            titulo: 'Projetos',
            descricao: 'Panorama dos projetos didáticos em andamento.',
            href: '/projetos',
            acao: 'Abrir projetos',
            itens: [
                {
                    rotulo: 'Projetos cadastrados',
                    valor: metricas.projetos.total,
                    apoio: 'Total de projetos visíveis para acompanhamento docente.',
                    icon: FolderKanban,
                    tom: 'bg-[#e7f5f2] text-[#0f766e] ring-[#bfe3dc]',
                },
                {
                    rotulo: 'Em iniciação',
                    valor: metricas.projetos.emIniciacao,
                    apoio: 'Projetos ainda na fase inicial do termo de abertura.',
                    icon: Target,
                    tom: 'bg-[#fff1e8] text-[#c45132] ring-[#ffd5bf]',
                },
            ],
        },
        {
            titulo: 'Turmas',
            descricao: 'Situação das turmas que sustentam os cadastros e os projetos.',
            href: '/turmas',
            acao: 'Gerenciar turmas',
            itens: [
                {
                    rotulo: 'Turmas cadastradas',
                    valor: metricas.turmas.total,
                    apoio: 'Histórico completo, incluindo turmas arquivadas.',
                    icon: School,
                    tom: 'bg-[#eef5e8] text-[#54732f] ring-[#d2e4bd]',
                },
                {
                    rotulo: 'Turmas ativas',
                    valor: metricas.turmas.ativas,
                    apoio: 'Turmas abertas para operação no semestre.',
                    icon: CheckCircle2,
                    tom: 'bg-[#e7f5f2] text-[#0f766e] ring-[#bfe3dc]',
                },
                {
                    rotulo: 'Aceitando cadastros',
                    valor: metricas.turmas.aceitandoCadastros,
                    apoio: 'Turmas ativas disponíveis para novas solicitações.',
                    icon: ClipboardList,
                    tom: 'bg-[#fff1e8] text-[#c45132] ring-[#ffd5bf]',
                },
            ],
        },
        {
            titulo: 'Alunos',
            descricao: 'Leitura rápida das solicitações e dos vínculos ativos.',
            href: '/alunos',
            acao: 'Revisar cadastros',
            itens: [
                {
                    rotulo: 'Cadastros de alunos',
                    valor: metricas.alunos.cadastros,
                    apoio: 'Solicitações registradas em qualquer status.',
                    icon: UsersRound,
                    tom: 'bg-[#eef5e8] text-[#54732f] ring-[#d2e4bd]',
                },
                {
                    rotulo: 'Cadastros pendentes',
                    valor: metricas.alunos.pendentes,
                    apoio: 'Solicitações aguardando aprovação ou reprovação.',
                    icon: Clock3,
                    tom: 'bg-[#fff7d6] text-[#8a6500] ring-[#f4df91]',
                },
                {
                    rotulo: 'Alunos aprovados',
                    valor: metricas.alunos.aprovadosAtivos,
                    apoio: 'Vínculos aprovados e ainda válidos.',
                    icon: UserCheck,
                    tom: 'bg-[#e7f5f2] text-[#0f766e] ring-[#bfe3dc]',
                },
            ],
        },
    ];

    return (
        <AppLayout titulo="Dashboard do professor" subtitulo="Indicadores centrais de projetos, turmas e alunos em um único lugar.">
            <Head title="Dashboard do professor" />

            <div className="grid gap-6">
                {grupos.map((grupo) => (
                    <section className="rounded-lg border border-[#dfe5d8] bg-white shadow-sm" key={grupo.titulo}>
                        <div className="flex flex-col gap-4 border-b border-[#dfe5d8] p-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 className="text-lg font-semibold text-[#17211f]">{grupo.titulo}</h2>
                                <p className="mt-1 text-sm leading-6 text-[#53635e]">{grupo.descricao}</p>
                            </div>
                            <Link
                                className="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-[#b9c4b7] px-3 text-sm font-semibold text-[#17211f] transition hover:bg-[#f6f7f2]"
                                href={grupo.href}
                            >
                                {grupo.acao}
                                <ArrowRight className="h-4 w-4" />
                            </Link>
                        </div>

                        <div className="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                            {grupo.itens.map((indicador) => (
                                <IndicadorCard indicador={indicador} key={indicador.rotulo} />
                            ))}
                        </div>
                    </section>
                ))}
            </div>
        </AppLayout>
    );
}

function IndicadorCard({ indicador }: { indicador: Indicador }) {
    const Icon = indicador.icon;

    return (
        <article className="min-h-40 rounded-lg border border-[#dfe5d8] bg-[#fbfcf7] p-5">
            <div className="flex items-start justify-between gap-4">
                <div className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-md ring-1 ${indicador.tom}`}>
                    <Icon className="h-5 w-5" />
                </div>
                <strong className="text-4xl font-semibold leading-none text-[#17211f]">{indicador.valor}</strong>
            </div>
            <h3 className="mt-5 text-sm font-semibold text-[#17211f]">{indicador.rotulo}</h3>
            <p className="mt-2 text-sm leading-6 text-[#53635e]">{indicador.apoio}</p>
        </article>
    );
}
