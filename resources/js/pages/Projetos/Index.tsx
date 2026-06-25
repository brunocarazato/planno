import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ClipboardCheck, Eye, FolderKanban, Plus, School, Target } from 'lucide-react';
import { FormEvent } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button, buttonVariants } from '../../shared/ui/button';

type TurmaOpcao = {
    id: number;
    nome: string;
    codigo: string;
    periodo: string | null;
};

type Projeto = {
    id: number;
    nome: string;
    codigo: string;
    descricao: string | null;
    situacaoFormatada: string;
    turma: TurmaOpcao;
};

type Metricas = {
    total: number;
    emIniciacao: number;
    turmasAtivas: number;
};

type ProjetoForm = {
    turma_id: string;
    nome: string;
    codigo: string;
    descricao: string;
};

type ProjetosIndexProps = {
    projetos: Projeto[];
    turmas: TurmaOpcao[];
    metricas: Metricas;
    flash?: {
        success?: string | null;
    };
};

const formularioInicial: ProjetoForm = {
    turma_id: '',
    nome: '',
    codigo: '',
    descricao: '',
};

export default function ProjetosIndex({ projetos, turmas, metricas, flash }: ProjetosIndexProps) {
    const form = useForm<ProjetoForm>(formularioInicial);
    const { auth } = usePage<{
        auth?: {
            user?: {
                tipo: string;
            } | null;
        };
    }>().props;
    const ehAluno = auth?.user?.tipo === 'aluno';
    const alunoPossuiTurmaAtiva = turmas.length > 0;
    const turmaDoAluno = turmas.length === 1 ? turmas[0] : null;

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.post('/projetos', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

    return (
        <AppLayout
            titulo="Projetos"
            subtitulo="Projetos didaticos vinculados a turmas ativas e preparados para o termo de abertura."
        >
            <Head title="Projetos" />

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-4 md:grid-cols-3">
                <Indicador icon={FolderKanban} rotulo="Projetos cadastrados" valor={metricas.total} />
                <Indicador icon={Target} rotulo="Em iniciacao" valor={metricas.emIniciacao} />
                <Indicador icon={School} rotulo="Turmas ativas" valor={metricas.turmasAtivas} />
            </section>

            <section className="mt-8 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-cyan-50 p-2 text-cyan-700">
                            <Plus className="h-5 w-5" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Novo projeto</h2>
                            <p className="text-sm text-slate-600">
                                Crie o projeto didatico inicial e associe-o a uma turma ativa.
                            </p>
                        </div>
                    </div>

                    <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
                        {ehAluno ? (
                            <div className="rounded-md border border-cyan-100 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
                                <p className="font-medium">Turma vinculada automaticamente</p>
                                <p className="mt-1">
                                    {turmaDoAluno ? `${turmaDoAluno.nome} (${turmaDoAluno.codigo})` : null}
                                    {!turmaDoAluno && alunoPossuiTurmaAtiva
                                        ? 'Seu projeto sera associado automaticamente a uma turma com vinculo aprovado.'
                                        : null}
                                    {!alunoPossuiTurmaAtiva
                                        ? 'Seu cadastro precisa ter uma turma ativa aprovada para criar projetos.'
                                        : null}
                                </p>
                                {form.errors.turma_id ? (
                                    <p className="mt-2 text-sm text-red-600">{form.errors.turma_id}</p>
                                ) : null}
                            </div>
                        ) : (
                            <div>
                                <label className="text-sm font-medium text-slate-700" htmlFor="turma_id">
                                    Turma
                                </label>
                                <select
                                    className="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-cyan-700 focus:ring-2 focus:ring-cyan-100"
                                    id="turma_id"
                                    onChange={(event) => form.setData('turma_id', event.target.value)}
                                    value={form.data.turma_id}
                                >
                                    <option value="">Selecione uma turma ativa</option>
                                    {turmas.map((turma) => (
                                        <option key={turma.id} value={turma.id}>
                                            {turma.nome} ({turma.codigo})
                                        </option>
                                    ))}
                                </select>
                                {form.errors.turma_id ? (
                                    <p className="mt-1 text-sm text-red-600">{form.errors.turma_id}</p>
                                ) : null}
                            </div>
                        )}

                        <CampoTexto
                            erro={form.errors.nome}
                            id="nome"
                            label="Nome"
                            onChange={(valor) => form.setData('nome', valor)}
                            placeholder="Aplicativo de gestao de tarefas academicas"
                            value={form.data.nome}
                        />
                        <CampoTexto
                            erro={form.errors.codigo}
                            id="codigo"
                            label="Codigo"
                            onChange={(valor) => form.setData('codigo', valor)}
                            placeholder="PROJ-2026-01"
                            value={form.data.codigo}
                        />

                        <div>
                            <label className="text-sm font-medium text-slate-700" htmlFor="descricao">
                                Descricao
                            </label>
                            <textarea
                                className="mt-1 min-h-28 w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-cyan-700 focus:ring-2 focus:ring-cyan-100"
                                id="descricao"
                                onChange={(event) => form.setData('descricao', event.target.value)}
                                placeholder="Contexto do projeto didatico, cliente ficticio ou problema escolhido pela turma."
                                value={form.data.descricao}
                            />
                            {form.errors.descricao ? (
                                <p className="mt-1 text-sm text-red-600">{form.errors.descricao}</p>
                            ) : null}
                        </div>

                        <Button
                            disabled={form.processing || (ehAluno ? !alunoPossuiTurmaAtiva : turmas.length === 0)}
                            type="submit"
                        >
                            Criar projeto
                        </Button>
                    </form>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between gap-4 border-b border-slate-200 p-6">
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Projetos cadastrados</h2>
                            <p className="mt-1 text-sm text-slate-600">
                                Acesse o detalhe para preencher e revisar o termo de abertura.
                            </p>
                        </div>
                        <ClipboardCheck className="h-5 w-5 shrink-0 text-cyan-700" />
                    </div>

                    {projetos.length === 0 ? (
                        <div className="p-8 text-center">
                            <p className="font-medium text-slate-950">Nenhum projeto cadastrado ainda.</p>
                            <p className="mt-2 text-sm text-slate-600">
                                Crie o primeiro projeto assim que houver uma turma ativa disponivel.
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-slate-200">
                            {projetos.map((projeto) => (
                                <article className="flex flex-col gap-4 p-5 xl:flex-row xl:items-start xl:justify-between" key={projeto.id}>
                                    <div>
                                        <div className="flex flex-wrap items-center gap-2">
                                            <h3 className="font-semibold text-slate-950">{projeto.nome}</h3>
                                            <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                {projeto.codigo}
                                            </span>
                                        </div>
                                        <p className="mt-1 text-sm text-slate-600">
                                            {projeto.turma.nome} ({projeto.turma.codigo})
                                        </p>
                                        {projeto.descricao ? (
                                            <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                                                {projeto.descricao}
                                            </p>
                                        ) : null}
                                        <span className="mt-4 inline-flex rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-medium text-cyan-800">
                                            {projeto.situacaoFormatada}
                                        </span>
                                    </div>

                                    <Link className={buttonVariants({ size: 'sm', variant: 'secondary' })} href={`/projetos/${projeto.id}`}>
                                        <Eye className="h-4 w-4" />
                                        Ver projeto
                                    </Link>
                                </article>
                            ))}
                        </div>
                    )}
                </div>
            </section>
        </AppLayout>
    );
}

function Indicador({
    icon: Icon,
    rotulo,
    valor,
}: {
    icon: typeof FolderKanban;
    rotulo: string;
    valor: number;
}) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <Icon className="h-5 w-5 text-cyan-700" />
            <p className="mt-4 text-sm text-slate-500">{rotulo}</p>
            <p className="mt-1 text-3xl font-semibold text-slate-950">{valor}</p>
        </div>
    );
}

function CampoTexto({
    erro,
    id,
    label,
    onChange,
    placeholder,
    value,
}: {
    erro?: string;
    id: keyof ProjetoForm;
    label: string;
    onChange: (valor: string) => void;
    placeholder: string;
    value: string;
}) {
    return (
        <div>
            <label className="text-sm font-medium text-slate-700" htmlFor={id}>
                {label}
            </label>
            <input
                className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-cyan-700 focus:ring-2 focus:ring-cyan-100"
                id={id}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                type="text"
                value={value}
            />
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}
