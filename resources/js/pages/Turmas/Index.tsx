import { Head, router, useForm } from '@inertiajs/react';
import { Archive, Ban, CheckCircle2, Pencil, Plus, RefreshCcw, UsersRound } from 'lucide-react';
import { FormEvent, useState } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button } from '../../shared/ui/button';
import { Dialog } from '../../shared/ui/dialog';

type Turma = {
    id: number;
    nome: string;
    codigo: string;
    periodo: string | null;
    ano: number | null;
    periodoFormatado: string | null;
    descricao: string | null;
    aceitaNovosCadastros: boolean;
    arquivadaEm: string | null;
    status: string;
};

type TurmaForm = {
    nome: string;
    periodo: string;
    ano: string;
    descricao: string;
};

type TurmasIndexProps = {
    turmas: Turma[];
    flash?: {
        success?: string | null;
    };
};

const formularioInicial: TurmaForm = {
    nome: '',
    periodo: '',
    ano: String(new Date().getFullYear()),
    descricao: '',
};

const TOTAL_DIGITOS_ANO = 4;

export default function TurmasIndex({ turmas, flash }: TurmasIndexProps) {
    const [turmaEmEdicao, setTurmaEmEdicao] = useState<Turma | null>(null);
    const [turmaParaArquivar, setTurmaParaArquivar] = useState<Turma | null>(null);
    const form = useForm<TurmaForm>(formularioInicial);

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        const ano = aplicarMascaraAno(form.data.ano);
        const erroAno = validarAno(ano);

        if (ano !== form.data.ano) {
            form.setData('ano', ano);
        }

        if (erroAno) {
            form.setError('ano', erroAno);

            return;
        }

        form.clearErrors('ano');

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
            periodo: turma.periodo ?? '',
            ano: turma.ano ? String(turma.ano) : '',
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
        setTurmaParaArquivar(turma);
    }

    function confirmarArquivamento() {
        if (!turmaParaArquivar) {
            return;
        }

        router.patch(`/turmas/${turmaParaArquivar.id}/arquivar`, {}, { preserveScroll: true });
        setTurmaParaArquivar(null);
    }

    return (
        <AppLayout
            titulo="Turmas"
            subtitulo="Gestão inicial das turmas que receberão cadastros de alunos e projetos didáticos."
        >
            <Head title="Turmas" />

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div className="rounded-lg border border-[#dfe5d8] bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-[#eff5ed] p-2 text-[#0f766e]">
                            {turmaEmEdicao ? <Pencil className="h-5 w-5" /> : <Plus className="h-5 w-5" />}
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-[#17211f]">
                                {turmaEmEdicao ? 'Editar turma' : 'Nova turma'}
                            </h2>
                            <p className="text-sm text-[#53635e]">
                                {turmaEmEdicao
                                    ? 'Atualize os dados básicos da turma selecionada.'
                                    : 'Crie uma turma ativa para receber futuras solicitações de alunos.'}
                            </p>
                        </div>
                    </div>

                    <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
                        <CampoTexto
                            erro={form.errors.nome}
                            id="nome"
                            label="Nome"
                            onChange={(valor) => form.setData('nome', valor)}
                            placeholder="3ADS"
                            value={form.data.nome}
                        />
                        <CampoPeriodo
                            erro={form.errors.periodo}
                            onChange={(valor) => form.setData('periodo', valor)}
                            value={form.data.periodo}
                        />
                        <CampoAno
                            erro={form.errors.ano}
                            onChange={(valor) => form.setData('ano', valor)}
                            value={form.data.ano}
                        />

                        <div>
                            <label className="text-sm font-medium text-[#51605c]" htmlFor="descricao">
                                Descrição
                            </label>
                            <textarea
                                className="mt-1 min-h-28 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                                id="descricao"
                                onChange={(event) => form.setData('descricao', event.target.value)}
                                placeholder="Contexto, professor responsável ou observações da turma."
                                value={form.data.descricao}
                            />
                            {form.errors.descricao ? (
                                <p className="mt-1 text-sm text-red-600">{form.errors.descricao}</p>
                            ) : null}
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <Button disabled={form.processing} type="submit">
                                {turmaEmEdicao ? 'Salvar alterações' : 'Criar turma'}
                            </Button>
                            {turmaEmEdicao ? (
                                <Button disabled={form.processing} onClick={cancelarEdicao} type="button" variant="secondary">
                                    Cancelar
                                </Button>
                            ) : null}
                        </div>
                    </form>
                </div>

                <div className="rounded-lg border border-[#dfe5d8] bg-white shadow-sm">
                    <div className="flex items-center justify-between gap-4 border-b border-[#dfe5d8] p-6">
                        <div>
                            <h2 className="text-lg font-semibold text-[#17211f]">Turmas cadastradas</h2>
                            <p className="mt-1 text-sm text-[#53635e]">
                                Controle se cada turma aceita novos cadastros antes da etapa de aprovação de alunos.
                            </p>
                        </div>
                        <UsersRound className="h-5 w-5 shrink-0 text-[#0f766e]" />
                    </div>

                    {turmas.length === 0 ? (
                        <div className="p-8 text-center">
                            <p className="font-medium text-[#17211f]">Nenhuma turma cadastrada ainda.</p>
                            <p className="mt-2 text-sm text-[#53635e]">
                                Crie a primeira turma para iniciar o fluxo administrativo do MVP.
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-[#dfe5d8]">
                            {turmas.map((turma) => (
                                <article className="p-5" key={turma.id}>
                                    <div className="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                        <div>
                                            <div className="flex flex-wrap items-center gap-2">
                                                <h3 className="font-semibold text-[#17211f]">{turma.nome}</h3>
                                                <span className="rounded-full bg-[#f4f7ef] px-2.5 py-1 text-xs font-medium text-[#51605c]">
                                                    {turma.codigo}
                                                </span>
                                            </div>
                                            <p className="mt-1 text-sm text-[#53635e]">
                                                {turma.periodoFormatado || 'Período não informado'}
                                            </p>
                                            {turma.descricao ? (
                                                <p className="mt-3 max-w-2xl text-sm leading-6 text-[#53635e]">
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

            <Dialog
                aberto={Boolean(turmaParaArquivar)}
                descricao={
                    turmaParaArquivar
                        ? `A turma ${turmaParaArquivar.codigo} será marcada como arquivada e não aceitará novos cadastros.`
                        : undefined
                }
                onClose={() => setTurmaParaArquivar(null)}
                titulo="Arquivar turma?"
            >
                <div className="flex flex-wrap justify-end gap-3">
                    <Button onClick={() => setTurmaParaArquivar(null)} type="button" variant="secondary">
                        Cancelar
                    </Button>
                    <Button onClick={confirmarArquivamento} type="button">
                        Arquivar turma
                    </Button>
                </div>
            </Dialog>

        </AppLayout>
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
            <label className="text-sm font-medium text-[#51605c]" htmlFor={id}>
                {label}
            </label>
            <input
                className="mt-1 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
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

function CampoPeriodo({
    erro,
    onChange,
    value,
}: {
    erro?: string;
    onChange: (valor: string) => void;
    value: string;
}) {
    return (
        <div>
            <label className="text-sm font-medium text-[#51605c]" htmlFor="periodo">
                Período
            </label>
            <select
                className="mt-1 w-full rounded-md border border-[#b9c4b7] bg-white px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                id="periodo"
                onChange={(event) => onChange(event.target.value)}
                value={value}
            >
                <option value="">Selecione o período</option>
                <option value="1">1º Semestre</option>
                <option value="2">2º Semestre</option>
            </select>
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}

function CampoAno({
    erro,
    onChange,
    value,
}: {
    erro?: string;
    onChange: (valor: string) => void;
    value: string;
}) {
    return (
        <div>
            <label className="text-sm font-medium text-[#51605c]" htmlFor="ano">
                Ano
            </label>
            <input
                className="mt-1 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
                aria-invalid={Boolean(erro)}
                id="ano"
                inputMode="numeric"
                maxLength={TOTAL_DIGITOS_ANO}
                minLength={TOTAL_DIGITOS_ANO}
                onChange={(event) => onChange(aplicarMascaraAno(event.target.value))}
                pattern={`[0-9]{${TOTAL_DIGITOS_ANO}}`}
                placeholder="2026"
                required
                type="text"
                value={value}
            />
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}

function aplicarMascaraAno(valor: string): string {
    return valor.replace(/\D/g, '').slice(0, TOTAL_DIGITOS_ANO);
}

function validarAno(valor: string): string | null {
    if (!new RegExp(`^\\d{${TOTAL_DIGITOS_ANO}}$`).test(valor)) {
        return 'Informe um ano com 4 dígitos.';
    }

    return null;
}

function StatusTurma({ turma }: { turma: Turma }) {
    const classes = turma.arquivadaEm
        ? 'border-[#dfe5d8] bg-[#f4f7ef] text-[#51605c]'
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
