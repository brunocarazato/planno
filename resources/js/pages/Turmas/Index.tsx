import { Head, Link, router, useForm } from '@inertiajs/react';
import { Archive, Ban, Check, CheckCircle2, ClipboardList, Pencil, Plus, RefreshCcw, UserPlus, UsersRound, X } from 'lucide-react';
import { FormEvent, useState } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button } from '../../shared/ui/button';

type Turma = {
    id: number;
    nome: string;
    codigo: string;
    periodo: string | null;
    descricao: string | null;
    aceitaNovosCadastros: boolean;
    arquivadaEm: string | null;
    status: string;
};

type Metricas = {
    total: number;
    ativas: number;
    aceitandoCadastros: number;
    cadastrosPendentes: number;
};

type CadastroPendente = {
    id: number;
    nome: string;
    ra: string;
    criadoEm: string | null;
    turma: {
        id: number;
        nome: string;
        codigo: string;
    };
};

type TurmaForm = {
    nome: string;
    codigo: string;
    periodo: string;
    descricao: string;
};

type TurmasIndexProps = {
    turmas: Turma[];
    cadastrosPendentes: CadastroPendente[];
    metricas: Metricas;
    flash?: {
        success?: string | null;
    };
};

const formularioInicial: TurmaForm = {
    nome: '',
    codigo: '',
    periodo: '',
    descricao: '',
};

export default function TurmasIndex({ turmas, cadastrosPendentes, metricas, flash }: TurmasIndexProps) {
    const [turmaEmEdicao, setTurmaEmEdicao] = useState<Turma | null>(null);
    const form = useForm<TurmaForm>(formularioInicial);

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (turmaEmEdicao) {
            form.put(`/turmas/${turmaEmEdicao.id}`, {
                preserveScroll: true,
                onSuccess: () => cancelarEdicao(),
            });

            return;
        }

        form.post('/turmas', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

    function editarTurma(turma: Turma) {
        setTurmaEmEdicao(turma);
        form.setData({
            nome: turma.nome,
            codigo: turma.codigo,
            periodo: turma.periodo ?? '',
            descricao: turma.descricao ?? '',
        });
        form.clearErrors();
    }

    function cancelarEdicao() {
        setTurmaEmEdicao(null);
        form.reset();
        form.clearErrors();
    }

    function alternarCadastros(turma: Turma) {
        const rota = turma.aceitaNovosCadastros
            ? `/turmas/${turma.id}/bloquear-cadastros`
            : `/turmas/${turma.id}/permitir-cadastros`;

        router.patch(rota, {}, { preserveScroll: true });
    }

    function arquivarTurma(turma: Turma) {
        if (!window.confirm(`Arquivar a turma ${turma.codigo}?`)) {
            return;
        }

        router.patch(`/turmas/${turma.id}/arquivar`, {}, { preserveScroll: true });
    }

    function aprovarCadastro(cadastro: CadastroPendente) {
        router.patch(`/cadastros-alunos/${cadastro.id}/aprovar`, {}, { preserveScroll: true });
    }

    function reprovarCadastro(cadastro: CadastroPendente) {
        const motivo = window.prompt(`Motivo da reprovacao de ${cadastro.nome} (opcional):`) ?? '';

        router.patch(
            `/cadastros-alunos/${cadastro.id}/reprovar`,
            { motivo_reprovacao: motivo },
            { preserveScroll: true },
        );
    }

    return (
        <AppLayout
            titulo="Turmas"
            subtitulo="Gestao inicial das turmas que receberao cadastros de alunos e projetos didaticos."
        >
            <Head title="Turmas" />

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-4 md:grid-cols-3">
                <Indicador rotulo="Turmas cadastradas" valor={metricas.total} />
                <Indicador rotulo="Turmas ativas" valor={metricas.ativas} />
                <Indicador rotulo="Aceitando cadastros" valor={metricas.aceitandoCadastros} />
            </section>

            <section className="mt-8 rounded-lg border border-slate-200 bg-white shadow-sm">
                <div className="flex flex-col gap-4 border-b border-slate-200 p-6 md:flex-row md:items-center md:justify-between">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-cyan-50 p-2 text-cyan-700">
                            <ClipboardList className="h-5 w-5" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Cadastros pendentes</h2>
                            <p className="text-sm text-slate-600">
                                Solicitações aguardando aprovacao para receber validade anual.
                            </p>
                        </div>
                    </div>
                    <div className="flex flex-wrap items-center gap-3">
                        <span className="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
                            {metricas.cadastrosPendentes} pendente(s)
                        </span>
                        <Link className="inline-flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-50" href="/cadastros-alunos/solicitar">
                            <UserPlus className="h-4 w-4" />
                            Solicitar cadastro
                        </Link>
                    </div>
                </div>

                {cadastrosPendentes.length === 0 ? (
                    <div className="p-8 text-center">
                        <p className="font-medium text-slate-950">Nenhum cadastro pendente.</p>
                        <p className="mt-2 text-sm text-slate-600">
                            Novas solicitacoes de alunos aparecerao aqui para aprovacao.
                        </p>
                    </div>
                ) : (
                    <div className="divide-y divide-slate-200">
                        {cadastrosPendentes.map((cadastro) => (
                            <article className="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between" key={cadastro.id}>
                                <div>
                                    <div className="flex flex-wrap items-center gap-2">
                                        <h3 className="font-semibold text-slate-950">{cadastro.nome}</h3>
                                        <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            RA {cadastro.ra}
                                        </span>
                                    </div>
                                    <p className="mt-1 text-sm text-slate-600">
                                        {cadastro.turma.nome} ({cadastro.turma.codigo})
                                    </p>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    <Button onClick={() => aprovarCadastro(cadastro)} size="sm" type="button">
                                        <Check className="h-4 w-4" />
                                        Aprovar
                                    </Button>
                                    <Button onClick={() => reprovarCadastro(cadastro)} size="sm" type="button" variant="secondary">
                                        <X className="h-4 w-4" />
                                        Reprovar
                                    </Button>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </section>

            <section className="mt-8 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-cyan-50 p-2 text-cyan-700">
                            {turmaEmEdicao ? <Pencil className="h-5 w-5" /> : <Plus className="h-5 w-5" />}
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">
                                {turmaEmEdicao ? 'Editar turma' : 'Nova turma'}
                            </h2>
                            <p className="text-sm text-slate-600">
                                {turmaEmEdicao
                                    ? 'Atualize os dados basicos da turma selecionada.'
                                    : 'Crie uma turma ativa para receber futuras solicitacoes de alunos.'}
                            </p>
                        </div>
                    </div>

                    <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
                        <CampoTexto
                            erro={form.errors.nome}
                            id="nome"
                            label="Nome"
                            onChange={(valor) => form.setData('nome', valor)}
                            placeholder="Gestao de Projetos 2026.1"
                            value={form.data.nome}
                        />
                        <CampoTexto
                            erro={form.errors.codigo}
                            id="codigo"
                            label="Codigo"
                            onChange={(valor) => form.setData('codigo', valor)}
                            placeholder="GP-2026-1A"
                            value={form.data.codigo}
                        />
                        <CampoTexto
                            erro={form.errors.periodo}
                            id="periodo"
                            label="Periodo"
                            onChange={(valor) => form.setData('periodo', valor)}
                            placeholder="1o semestre de 2026"
                            value={form.data.periodo}
                        />

                        <div>
                            <label className="text-sm font-medium text-slate-700" htmlFor="descricao">
                                Descricao
                            </label>
                            <textarea
                                className="mt-1 min-h-28 w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-cyan-700 focus:ring-2 focus:ring-cyan-100"
                                id="descricao"
                                onChange={(event) => form.setData('descricao', event.target.value)}
                                placeholder="Contexto, professor responsavel ou observacoes da turma."
                                value={form.data.descricao}
                            />
                            {form.errors.descricao ? (
                                <p className="mt-1 text-sm text-red-600">{form.errors.descricao}</p>
                            ) : null}
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <Button disabled={form.processing} type="submit">
                                {turmaEmEdicao ? 'Salvar alteracoes' : 'Criar turma'}
                            </Button>
                            {turmaEmEdicao ? (
                                <Button disabled={form.processing} onClick={cancelarEdicao} type="button" variant="secondary">
                                    Cancelar
                                </Button>
                            ) : null}
                        </div>
                    </form>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between gap-4 border-b border-slate-200 p-6">
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Turmas cadastradas</h2>
                            <p className="mt-1 text-sm text-slate-600">
                                Controle se cada turma aceita novos cadastros antes da etapa de aprovacao de alunos.
                            </p>
                        </div>
                        <UsersRound className="h-5 w-5 shrink-0 text-cyan-700" />
                    </div>

                    {turmas.length === 0 ? (
                        <div className="p-8 text-center">
                            <p className="font-medium text-slate-950">Nenhuma turma cadastrada ainda.</p>
                            <p className="mt-2 text-sm text-slate-600">
                                Crie a primeira turma para iniciar o fluxo administrativo do MVP.
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-slate-200">
                            {turmas.map((turma) => (
                                <article className="p-5" key={turma.id}>
                                    <div className="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                        <div>
                                            <div className="flex flex-wrap items-center gap-2">
                                                <h3 className="font-semibold text-slate-950">{turma.nome}</h3>
                                                <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                    {turma.codigo}
                                                </span>
                                            </div>
                                            <p className="mt-1 text-sm text-slate-600">
                                                {turma.periodo || 'Periodo nao informado'}
                                            </p>
                                            {turma.descricao ? (
                                                <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                                                    {turma.descricao}
                                                </p>
                                            ) : null}
                                            <StatusTurma turma={turma} />
                                        </div>

                                        <div className="flex flex-wrap gap-2">
                                            <Button onClick={() => editarTurma(turma)} size="sm" type="button" variant="secondary">
                                                <Pencil className="h-4 w-4" />
                                                Editar
                                            </Button>
                                            <Button
                                                disabled={Boolean(turma.arquivadaEm)}
                                                onClick={() => alternarCadastros(turma)}
                                                size="sm"
                                                type="button"
                                                variant="secondary"
                                            >
                                                {turma.aceitaNovosCadastros ? (
                                                    <Ban className="h-4 w-4" />
                                                ) : (
                                                    <RefreshCcw className="h-4 w-4" />
                                                )}
                                                {turma.aceitaNovosCadastros ? 'Bloquear' : 'Permitir'}
                                            </Button>
                                            <Button
                                                disabled={Boolean(turma.arquivadaEm)}
                                                onClick={() => arquivarTurma(turma)}
                                                size="sm"
                                                type="button"
                                                variant="ghost"
                                            >
                                                <Archive className="h-4 w-4" />
                                                Arquivar
                                            </Button>
                                        </div>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </div>
            </section>
        </AppLayout>
    );
}

function Indicador({ rotulo, valor }: { rotulo: string; valor: number }) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p className="text-sm text-slate-500">{rotulo}</p>
            <p className="mt-2 text-3xl font-semibold text-slate-950">{valor}</p>
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
    id: keyof TurmaForm;
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

function StatusTurma({ turma }: { turma: Turma }) {
    const classes = turma.arquivadaEm
        ? 'border-slate-200 bg-slate-100 text-slate-700'
        : turma.aceitaNovosCadastros
          ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
          : 'border-amber-200 bg-amber-50 text-amber-800';

    return (
        <div className={`mt-4 inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium ${classes}`}>
            {!turma.arquivadaEm && turma.aceitaNovosCadastros ? <CheckCircle2 className="h-3.5 w-3.5" /> : null}
            {turma.status}
        </div>
    );
}
