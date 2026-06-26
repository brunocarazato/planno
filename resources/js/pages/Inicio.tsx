import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    BookOpenCheck,
    CheckCircle2,
    ClipboardList,
    Gauge,
    GraduationCap,
    LayoutDashboard,
    Route,
    UsersRound,
} from 'lucide-react';
import { useEffect, useState, type ComponentType } from 'react';

import { LoginDialog } from '../shared/autenticacao/LoginDialog';

const trilhas = [
    {
        titulo: 'Projetos tradicionais',
        descricao: 'Termo de abertura, escopo, cronograma, custos, riscos e partes interessadas conectados a entregas reais.',
        icon: ClipboardList,
        tom: 'bg-teal-50 text-teal-800 ring-teal-200',
    },
    {
        titulo: 'Projetos ageis',
        descricao: 'Backlog, ciclos curtos, quadro de trabalho e acompanhamento visual para aproximar teoria e pratica.',
        icon: Gauge,
        tom: 'bg-orange-50 text-orange-800 ring-orange-200',
    },
    {
        titulo: 'Aprendizagem guiada',
        descricao: 'Rubricas, artefatos e orientacao de professor para transformar cada decisao do grupo em evidencia pedagogica.',
        icon: GraduationCap,
        tom: 'bg-emerald-50 text-emerald-800 ring-emerald-200',
    },
];

const jornada = [
    'Forme turmas e grupos de projeto',
    'Conduza o termo de abertura',
    'Planeje por escopo, tempo, custos e riscos',
    'Compare fluxo tradicional e ciclos ageis',
    'Avalie artefatos com criterios claros',
];

const metricas = [
    { valor: '01', rotulo: 'ambiente para professor e aluno' },
    { valor: '02', rotulo: 'abordagens de gestao na mesma trilha' },
    { valor: '05', rotulo: 'etapas didaticas ate a avaliacao' },
];

export default function Inicio() {
    const { url } = usePage();
    const [loginAberto, setLoginAberto] = useState(false);

    useEffect(() => {
        if (url.includes('login=1')) {
            setLoginAberto(true);
        }
    }, [url]);

    return (
        <main className="min-h-screen bg-[#f6f7f2] text-[#17211f]">
            <Head title="Inicio" />

            <section className="relative isolate overflow-hidden border-b border-[#dfe5d8] bg-[#fbfcf7]">
                <div className="absolute inset-0 -z-10 bg-[linear-gradient(rgba(23,33,31,0.055)_1px,transparent_1px),linear-gradient(90deg,rgba(23,33,31,0.055)_1px,transparent_1px)] bg-[size:44px_44px]" />
                <div className="absolute left-0 top-0 -z-10 h-full w-2/5 bg-[#eff5ed]" />

                <header className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                    <Link className="inline-flex items-center gap-3 text-[#17211f]" href="/">
                        <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-[#17211f] text-white">
                            <Route className="h-5 w-5" />
                        </span>
                        <span className="text-xl font-semibold">Planno</span>
                    </Link>

                    <nav className="flex items-center gap-2">
                        <Link
                            className="hidden rounded-md px-3 py-2 text-sm font-semibold text-[#51605c] hover:bg-[#edf2e9] sm:inline-flex"
                            href="/cadastros-alunos/solicitar"
                        >
                            Cadastro aluno
                        </Link>
                        <button
                            className="inline-flex h-10 items-center gap-2 rounded-md bg-[#17211f] px-4 text-sm font-semibold text-white shadow-sm hover:bg-[#273633]"
                            onClick={() => setLoginAberto(true)}
                            type="button"
                        >
                            Entrar
                            <ArrowRight className="h-4 w-4" />
                        </button>
                    </nav>
                </header>

                <div className="mx-auto grid min-h-[72svh] max-w-7xl items-center gap-10 px-5 pb-12 pt-6 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:pb-16">
                    <div className="max-w-3xl py-6">
                        <span className="inline-flex items-center gap-2 rounded-md border border-[#cdd9cf] bg-white px-3 py-2 text-sm font-semibold text-[#0f766e] shadow-sm">
                            <BookOpenCheck className="h-4 w-4" />
                            Gestao de projetos ensinada pela pratica
                        </span>

                        <h1 className="mt-5 max-w-4xl text-5xl font-semibold leading-[1.02] text-[#17211f] sm:text-6xl lg:text-7xl">
                            O laboratorio didatico para aprender a planejar, executar e avaliar projetos.
                        </h1>

                        <p className="mt-6 max-w-2xl text-lg leading-8 text-[#53635e]">
                            O Planno organiza turmas, grupos e artefatos para que alunos experimentem gestao
                            tradicional e agil dentro de uma jornada educacional acompanhada por evidencias.
                        </p>

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Link
                                className="inline-flex h-12 items-center justify-center gap-2 rounded-md bg-[#f06f45] px-5 text-sm font-bold text-white shadow-lg shadow-[#f06f45]/18 hover:bg-[#dc5e38]"
                                href="/cadastros-alunos/solicitar"
                            >
                                Solicitar cadastro
                                <ArrowRight className="h-4 w-4" />
                            </Link>
                            <Link
                                className="inline-flex h-12 items-center justify-center gap-2 rounded-md border border-[#b9c4b7] bg-white px-5 text-sm font-bold text-[#17211f] hover:bg-[#f4f7ef]"
                                href="/projetos"
                            >
                                Ver projetos
                                <LayoutDashboard className="h-4 w-4" />
                            </Link>
                        </div>
                    </div>

                    <DiagramaPlanno />
                </div>
            </section>

            <section className="border-y border-[#dfe5d8] bg-[#f6f7f2]">
                <div className="mx-auto grid max-w-7xl gap-3 px-5 py-5 sm:grid-cols-3 sm:px-8">
                    {metricas.map((metrica) => (
                        <div className="flex items-center gap-4 rounded-lg bg-white px-4 py-4 shadow-sm" key={metrica.rotulo}>
                            <strong className="text-3xl font-semibold text-[#0f766e]">{metrica.valor}</strong>
                            <span className="text-sm font-medium leading-5 text-[#4d5d58]">{metrica.rotulo}</span>
                        </div>
                    ))}
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:py-20">
                <div className="grid gap-10 lg:grid-cols-[0.76fr_1.24fr] lg:items-end">
                    <div>
                        <p className="text-sm font-bold text-[#0f766e]">Da sala de aula ao artefato</p>
                        <h2 className="mt-3 text-4xl font-semibold leading-tight text-[#17211f]">
                            Um produto feito para ensinar decisao, nao apenas registrar tarefas.
                        </h2>
                    </div>
                    <p className="max-w-3xl text-lg leading-8 text-[#53635e]">
                        A pagina inicial agora apresenta o Planno como uma plataforma educacional: cada modulo existe
                        para transformar conceitos de gestao em producoes avaliaveis, comparaveis e acompanhadas.
                    </p>
                </div>

                <div className="mt-10 grid gap-5 md:grid-cols-3">
                    {trilhas.map((trilha) => (
                        <TrilhaCard key={trilha.titulo} {...trilha} />
                    ))}
                </div>
            </section>

            <section className="bg-[#17211f] text-white">
                <div className="mx-auto grid max-w-7xl gap-10 px-5 py-14 sm:px-8 lg:grid-cols-[0.95fr_1.05fr] lg:py-18">
                    <div>
                        <p className="text-sm font-bold text-[#6de1d2]">Trilha pedagogica</p>
                        <h2 className="mt-3 text-4xl font-semibold leading-tight">Planejamento com contexto, pratica e feedback.</h2>
                        <p className="mt-5 max-w-2xl text-base leading-7 text-white/72">
                            O foco educacional aparece na sequencia das atividades: professor estrutura, aluno
                            constroi, grupo decide e a avaliacao nasce dos artefatos gerados no caminho.
                        </p>
                    </div>

                    <ol className="grid gap-3">
                        {jornada.map((item, index) => (
                            <li
                                className="grid grid-cols-[3rem_1fr] items-center gap-4 rounded-lg border border-white/12 bg-white/7 p-4"
                                key={item}
                            >
                                <span className="flex h-12 w-12 items-center justify-center rounded-lg bg-[#f6f7f2] text-sm font-bold text-[#17211f]">
                                    {String(index + 1).padStart(2, '0')}
                                </span>
                                <span className="text-base font-semibold text-white">{item}</span>
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            <section className="bg-[#eef2e8]">
                <div className="mx-auto flex max-w-7xl flex-col gap-6 px-5 py-10 sm:px-8 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p className="text-sm font-bold text-[#c45132]">Comece pelo fluxo da sua turma</p>
                        <h2 className="mt-2 text-3xl font-semibold text-[#17211f]">
                            Acesse o ambiente ou abra o cadastro de alunos.
                        </h2>
                    </div>
                    <div className="flex flex-col gap-3 sm:flex-row">
                        <button
                            className="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-[#17211f] px-5 text-sm font-bold text-white hover:bg-[#273633]"
                            onClick={() => setLoginAberto(true)}
                            type="button"
                        >
                            Entrar no Planno
                            <ArrowRight className="h-4 w-4" />
                        </button>
                        <Link
                            className="inline-flex h-11 items-center justify-center gap-2 rounded-md border border-[#b9c4b7] bg-white px-5 text-sm font-bold text-[#17211f] hover:bg-[#f7f8f4]"
                            href="/cadastros-alunos/solicitar"
                        >
                            Cadastro aluno
                            <UsersRound className="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </section>

            <LoginDialog aberto={loginAberto} onClose={() => setLoginAberto(false)} />
        </main>
    );
}

function TrilhaCard({
    descricao,
    icon: Icon,
    titulo,
    tom,
}: {
    descricao: string;
    icon: ComponentType<{ className?: string }>;
    titulo: string;
    tom: string;
}) {
    return (
        <article className="rounded-lg border border-[#dfe5d8] bg-white p-6 shadow-sm">
            <div className={`flex h-12 w-12 items-center justify-center rounded-lg ring-1 ${tom}`}>
                <Icon className="h-6 w-6" />
            </div>
            <h3 className="mt-5 text-xl font-semibold text-[#17211f]">{titulo}</h3>
            <p className="mt-3 text-base leading-7 text-[#586762]">{descricao}</p>
        </article>
    );
}

function DiagramaPlanno() {
    const kanban = [
        ['Escopo', 'Risco'],
        ['Sprint', 'Rubrica'],
        ['Entrega', 'Feedback'],
    ];

    return (
        <div className="relative min-h-[420px] overflow-hidden rounded-lg border border-[#d9e2d7] bg-white p-5 shadow-2xl shadow-[#17211f]/8">
            <div className="absolute inset-0 bg-[linear-gradient(rgba(15,118,110,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(15,118,110,0.08)_1px,transparent_1px)] bg-[size:28px_28px]" />
            <svg
                aria-hidden="true"
                className="absolute inset-0 h-full w-full"
                fill="none"
                preserveAspectRatio="none"
                viewBox="0 0 640 430"
            >
                <path
                    d="M72 116 C172 54 251 62 327 124 S462 218 574 136"
                    stroke="#0f766e"
                    strokeDasharray="8 12"
                    strokeLinecap="round"
                    strokeWidth="2"
                />
                <path d="M82 314 L224 236 L376 296 L558 210" stroke="#f06f45" strokeLinecap="round" strokeWidth="3" />
                <circle cx="82" cy="314" fill="#f6f7f2" r="8" stroke="#f06f45" strokeWidth="3" />
                <circle cx="224" cy="236" fill="#f6f7f2" r="8" stroke="#f06f45" strokeWidth="3" />
                <circle cx="376" cy="296" fill="#f6f7f2" r="8" stroke="#f06f45" strokeWidth="3" />
                <circle cx="558" cy="210" fill="#f6f7f2" r="8" stroke="#f06f45" strokeWidth="3" />
            </svg>

            <div className="relative grid h-full gap-5">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="text-xs font-bold uppercase text-[#0f766e]">Mapa de aprendizagem</p>
                        <h2 className="mt-1 text-2xl font-semibold text-[#17211f]">Projeto integrador</h2>
                    </div>
                    <span className="rounded-md border border-[#d9e2d7] bg-[#f6f7f2] px-3 py-2 text-sm font-bold text-[#51605c]">
                        Aula 04
                    </span>
                </div>

                <div className="grid gap-4 lg:grid-cols-[0.78fr_1.22fr]">
                    <div className="space-y-3">
                        {['Iniciacao', 'Planejamento', 'Execucao'].map((etapa, index) => (
                            <div
                                className="flex items-center gap-3 rounded-lg border border-[#dfe5d8] bg-[#fbfcf7] p-3"
                                key={etapa}
                            >
                                <span className="flex h-9 w-9 items-center justify-center rounded-md bg-[#17211f] text-xs font-bold text-white">
                                    {index + 1}
                                </span>
                                <div>
                                    <p className="text-sm font-bold text-[#17211f]">{etapa}</p>
                                    <p className="text-xs font-medium text-[#66756f]">evidencia do grupo</p>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="rounded-lg border border-[#dfe5d8] bg-white p-4">
                        <div className="mb-3 flex items-center justify-between">
                            <p className="text-sm font-bold text-[#17211f]">Quadro hibrido</p>
                            <Gauge className="h-5 w-5 text-[#f06f45]" />
                        </div>
                        <div className="grid grid-cols-3 gap-3">
                            {kanban.map((coluna, index) => (
                                <div className="min-h-40 rounded-md bg-[#f6f7f2] p-2" key={index}>
                                    <div className="mb-2 h-1.5 rounded-full bg-[#0f766e]" />
                                    <div className="space-y-2">
                                        {coluna.map((item, itemIndex) => (
                                            <div
                                                className={`rounded-md border bg-white px-2 py-2 text-xs font-bold shadow-sm ${
                                                    itemIndex === 0
                                                        ? 'border-[#cdd9cf] text-[#17211f]'
                                                        : 'border-[#ffd7c9] text-[#c45132]'
                                                }`}
                                                key={item}
                                            >
                                                {item}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="grid gap-3 sm:grid-cols-3">
                    {[
                        ['PMBOK', 'termo + cronograma'],
                        ['Agil', 'backlog + ciclo'],
                        ['Ensino', 'rubrica + feedback'],
                    ].map(([titulo, texto]) => (
                        <div className="rounded-lg border border-[#dfe5d8] bg-[#fbfcf7] p-3" key={titulo}>
                            <div className="mb-3 flex items-center gap-2">
                                <CheckCircle2 className="h-4 w-4 text-[#0f766e]" />
                                <p className="text-sm font-bold text-[#17211f]">{titulo}</p>
                            </div>
                            <p className="text-xs font-medium leading-5 text-[#66756f]">{texto}</p>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
