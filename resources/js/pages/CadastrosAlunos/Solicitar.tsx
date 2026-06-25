import { Head, useForm } from '@inertiajs/react';
import { ClipboardCheck, Send, UsersRound } from 'lucide-react';
import { FormEvent } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button } from '../../shared/ui/button';

type TurmaDisponivel = {
    id: number;
    nome: string;
    codigo: string;
    periodo: string | null;
};

type CadastroAlunoForm = {
    turma_id: string;
    nome: string;
    ra: string;
    password: string;
    password_confirmation: string;
};

type SolicitarCadastroProps = {
    turmas: TurmaDisponivel[];
    flash?: {
        success?: string | null;
    };
};

const formularioInicial: CadastroAlunoForm = {
    turma_id: '',
    nome: '',
    ra: '',
    password: '',
    password_confirmation: '',
};

export default function SolicitarCadastro({ turmas, flash }: SolicitarCadastroProps) {
    const form = useForm<CadastroAlunoForm>(formularioInicial);

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.post('/cadastros-alunos', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

    return (
        <AppLayout
            titulo="Solicitar cadastro"
            subtitulo="Informe seus dados para participar de uma turma ativa. A aprovacao depende do professor ou administrador."
        >
            <Head title="Solicitar cadastro de aluno" />

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-cyan-50 p-2 text-cyan-700">
                            <ClipboardCheck className="h-5 w-5" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Dados do aluno</h2>
                            <p className="text-sm text-slate-600">
                                O cadastro ficara pendente ate a avaliacao da equipe responsavel.
                            </p>
                        </div>
                    </div>

                    <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
                        <div>
                            <label className="text-sm font-medium text-slate-700" htmlFor="turma_id">
                                Turma
                            </label>
                            <select
                                className="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none transition focus:border-cyan-700 focus:ring-2 focus:ring-cyan-100"
                                disabled={turmas.length === 0}
                                id="turma_id"
                                onChange={(event) => form.setData('turma_id', event.target.value)}
                                value={form.data.turma_id}
                            >
                                <option value="">Selecione uma turma</option>
                                {turmas.map((turma) => (
                                    <option key={turma.id} value={String(turma.id)}>
                                        {turma.nome} ({turma.codigo})
                                    </option>
                                ))}
                            </select>
                            {form.errors.turma_id ? (
                                <p className="mt-1 text-sm text-red-600">{form.errors.turma_id}</p>
                            ) : null}
                        </div>

                        <CampoTexto
                            erro={form.errors.nome}
                            id="nome"
                            label="Nome completo"
                            onChange={(valor) => form.setData('nome', valor)}
                            placeholder="Nome do aluno"
                            type="text"
                            value={form.data.nome}
                        />
                        <CampoTexto
                            erro={form.errors.ra}
                            id="ra"
                            label="RA"
                            onChange={(valor) => form.setData('ra', valor)}
                            placeholder="Registro academico"
                            type="text"
                            value={form.data.ra}
                        />
                        <CampoTexto
                            erro={form.errors.password}
                            id="password"
                            label="Senha"
                            onChange={(valor) => form.setData('password', valor)}
                            placeholder="Minimo de 8 caracteres"
                            type="password"
                            value={form.data.password}
                        />
                        <CampoTexto
                            erro={form.errors.password_confirmation}
                            id="password_confirmation"
                            label="Confirmar senha"
                            onChange={(valor) => form.setData('password_confirmation', valor)}
                            placeholder="Repita a senha"
                            type="password"
                            value={form.data.password_confirmation}
                        />

                        <Button disabled={form.processing || turmas.length === 0} type="submit">
                            <Send className="h-4 w-4" />
                            Solicitar cadastro
                        </Button>
                    </form>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="rounded-md bg-slate-100 p-2 text-slate-700">
                            <UsersRound className="h-5 w-5" />
                        </div>
                        <div>
                            <h2 className="text-lg font-semibold text-slate-950">Turmas abertas</h2>
                            <p className="text-sm text-slate-600">
                                Apenas turmas ativas e liberadas aparecem para solicitacao.
                            </p>
                        </div>
                    </div>

                    {turmas.length === 0 ? (
                        <div className="mt-6 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            Nenhuma turma esta recebendo novos cadastros no momento.
                        </div>
                    ) : (
                        <div className="mt-6 divide-y divide-slate-200 rounded-md border border-slate-200">
                            {turmas.map((turma) => (
                                <article className="p-4" key={turma.id}>
                                    <div className="flex flex-wrap items-center gap-2">
                                        <h3 className="font-semibold text-slate-950">{turma.nome}</h3>
                                        <span className="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            {turma.codigo}
                                        </span>
                                    </div>
                                    <p className="mt-1 text-sm text-slate-600">
                                        {turma.periodo || 'Periodo nao informado'}
                                    </p>
                                </article>
                            ))}
                        </div>
                    )}
                </div>
            </section>
        </AppLayout>
    );
}

function CampoTexto({
    erro,
    id,
    label,
    onChange,
    placeholder,
    type,
    value,
}: {
    erro?: string;
    id: keyof Pick<CadastroAlunoForm, 'nome' | 'ra' | 'password' | 'password_confirmation'>;
    label: string;
    onChange: (valor: string) => void;
    placeholder: string;
    type: 'password' | 'text';
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
                type={type}
                value={value}
            />
            {erro ? <p className="mt-1 text-sm text-red-600">{erro}</p> : null}
        </div>
    );
}
