import { Head, Link } from '@inertiajs/react';
import { Archive, BookOpenCheck, CheckCircle2, FolderKanban, UsersRound } from 'lucide-react';
import type { ComponentType } from 'react';

import { AppLayout } from '../shared/layouts/AppLayout';
import { Button, buttonVariants } from '../shared/ui/button';

const fases = [
    {
        nome: 'Fase 0',
        titulo: 'Fundacao tecnica',
        status: 'Em andamento',
        descricao: 'Laravel, Inertia, React, TypeScript, Tailwind e estrutura modular inicial.',
    },
    {
        nome: 'Fase 1',
        titulo: 'Turmas e cadastros',
        status: 'Proxima vertical',
        descricao: 'Criacao de turmas, solicitacao de cadastro de alunos e fluxo de aprovacao.',
    },
    {
        nome: 'Fase 2',
        titulo: 'Projetos didaticos',
        status: 'Planejada',
        descricao: 'Projetos vinculados a turmas ativas e termo de abertura.',
    },
];

const modulos = [
    'Turmas',
    'Projetos',
    'GruposDeProcessos',
    'GerenciamentoDeEscopo',
    'GerenciamentoDeCronograma',
    'GerenciamentoDeCustos',
    'GerenciamentoDeRiscos',
    'GerenciamentoDasPartesInteressadas',
    'Avaliacoes',
    'Simulacoes',
];

export default function Inicio() {
    return (
        <AppLayout
            titulo="Fundacao do MVP"
            subtitulo="Primeira entrega navegavel da ferramenta pedagogica de gestao de projetos."
        >
            <Head title="Inicio" />

            <section className="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-start gap-4">
                        <div className="rounded-md bg-cyan-50 p-3 text-cyan-700">
                            <BookOpenCheck className="h-6 w-6" />
                        </div>
                        <div>
                            <p className="text-sm font-medium text-cyan-700">MVP primeiro semestre</p>
                            <h2 className="mt-2 text-3xl font-semibold text-slate-950">
                                Experiencia guiada pelos grupos de processos do PMBOK
                            </h2>
                            <p className="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                                A aplicacao nasce pelo fluxo administrativo de turmas e cadastros de alunos, preparando
                                a trilha para projetos didaticos, artefatos e avaliacao por rubrica.
                            </p>
                            <div className="mt-6 flex flex-wrap gap-3">
                                <Link className={buttonVariants()} href="/turmas">
                                    Iniciar fase de Turmas
                                </Link>
                                <Button type="button" variant="secondary">
                                    Ver plano do MVP
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                    <Resumo icon={UsersRound} rotulo="Turmas" valor="Base criada" />
                    <Resumo icon={FolderKanban} rotulo="Projetos" valor="Modulo previsto" />
                    <Resumo icon={Archive} rotulo="Cadastros" valor="Fluxo seguinte" />
                </div>
            </section>

            <section className="mt-8 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm" id="turmas">
                    <h2 className="text-lg font-semibold text-slate-950">Entregas verticais</h2>
                    <div className="mt-5 space-y-4">
                        {fases.map((fase) => (
                            <article className="rounded-md border border-slate-200 p-4" key={fase.nome}>
                                <div className="flex items-center justify-between gap-3">
                                    <p className="text-sm font-semibold text-cyan-700">{fase.nome}</p>
                                    <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                        {fase.status}
                                    </span>
                                </div>
                                <h3 className="mt-2 font-semibold text-slate-950">{fase.titulo}</h3>
                                <p className="mt-1 text-sm leading-6 text-slate-600">{fase.descricao}</p>
                            </article>
                        ))}
                    </div>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm" id="projetos">
                    <h2 className="text-lg font-semibold text-slate-950">Modulos iniciais</h2>
                    <p className="mt-2 text-sm text-slate-600">
                        Estrutura criada em <code className="rounded bg-slate-100 px-1.5 py-0.5">app/Modules</code>,
                        com nomes tecnicos em portugues sem acentos.
                    </p>
                    <div className="mt-5 grid gap-3 sm:grid-cols-2">
                        {modulos.map((modulo) => (
                            <div className="flex items-center gap-3 rounded-md border border-slate-200 p-3" key={modulo}>
                                <CheckCircle2 className="h-4 w-4 text-cyan-700" />
                                <span className="text-sm font-medium text-slate-800">{modulo}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}

function Resumo({
    icon: Icon,
    rotulo,
    valor,
}: {
    icon: ComponentType<{ className?: string }>;
    rotulo: string;
    valor: string;
}) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <Icon className="h-5 w-5 text-cyan-700" />
            <p className="mt-4 text-sm text-slate-500">{rotulo}</p>
            <p className="mt-1 font-semibold text-slate-950">{valor}</p>
        </div>
    );
}
