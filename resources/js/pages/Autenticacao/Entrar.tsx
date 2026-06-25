import { Head, Link, useForm } from '@inertiajs/react';
import { LogIn, UserRound } from 'lucide-react';
import { FormEvent } from 'react';

import { AppLayout } from '../../shared/layouts/AppLayout';
import { Button, buttonVariants } from '../../shared/ui/button';

type EntrarForm = {
    ra: string;
    password: string;
};

type EntrarProps = {
    flash?: {
        success?: string | null;
    };
};

export default function Entrar({ flash }: EntrarProps) {
    const form = useForm<EntrarForm>({
        ra: '',
        password: '',
    });

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.post('/entrar', {
            preserveScroll: true,
            onSuccess: () => form.reset('password'),
        });
    }

    return (
        <AppLayout titulo="Entrar" subtitulo="Acesse a aplicacao com RA e senha cadastrados.">
            <Head title="Entrar" />

            {flash?.success ? (
                <div className="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {flash.success}
                </div>
            ) : null}

            <section className="mx-auto max-w-xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex items-center gap-3">
                    <div className="rounded-md bg-cyan-50 p-2 text-cyan-700">
                        <UserRound className="h-5 w-5" />
                    </div>
                    <div>
                        <h2 className="text-lg font-semibold text-slate-950">Login simples</h2>
                        <p className="text-sm text-slate-600">
                            Alunos entram com o RA informado no cadastro.
                        </p>
                    </div>
                </div>

                <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
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
                        placeholder="Sua senha"
                        type="password"
                        value={form.data.password}
                    />

                    <div className="flex flex-wrap gap-3">
                        <Button disabled={form.processing} type="submit">
                            <LogIn className="h-4 w-4" />
                            Entrar
                        </Button>
                        <Link className={buttonVariants({ variant: 'secondary' })} href="/cadastros-alunos/solicitar">
                            Criar cadastro de aluno
                        </Link>
                    </div>
                </form>
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
    id: keyof EntrarForm;
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
