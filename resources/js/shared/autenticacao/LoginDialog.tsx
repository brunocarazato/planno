import { Link, useForm } from '@inertiajs/react';
import { LogIn, UserRound } from 'lucide-react';
import { FormEvent } from 'react';

import { buttonVariants } from '../ui/button';
import { Dialog } from '../ui/dialog';

type LoginDialogProps = {
    aberto: boolean;
    onClose: () => void;
};

type EntrarForm = {
    ra: string;
    password: string;
};

export function LoginDialog({ aberto, onClose }: LoginDialogProps) {
    const form = useForm<EntrarForm>({
        ra: '',
        password: '',
    });

    function fechar() {
        form.clearErrors();
        form.reset('password');
        onClose();
    }

    function enviarFormulario(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        form.post('/entrar', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('password');
                onClose();
            },
        });
    }

    return (
        <Dialog
            aberto={aberto}
            className="max-w-md"
            descricao="Acesse com RA e senha cadastrados."
            onClose={fechar}
            titulo="Entrar no Planno"
        >
            <div className="flex items-center gap-3 rounded-lg border border-[#dfe5d8] bg-[#fbfcf7] p-4">
                <div className="rounded-md bg-[#eff5ed] p-2 text-[#0f766e]">
                    <UserRound className="h-5 w-5" />
                </div>
                <div>
                    <h3 className="text-sm font-semibold text-[#17211f]">Login simples</h3>
                    <p className="text-sm text-[#53635e]">Alunos entram com o RA informado no cadastro.</p>
                </div>
            </div>

            <form className="mt-6 space-y-5" onSubmit={enviarFormulario}>
                <CampoTexto
                    erro={form.errors.ra}
                    id="ra"
                    label="RA"
                    onChange={(valor) => form.setData('ra', valor)}
                    placeholder="Registro acadêmico"
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

                <div className="flex flex-col gap-3 sm:flex-row">
                    <button className={buttonVariants()} disabled={form.processing} type="submit">
                        <LogIn className="h-4 w-4" />
                        Entrar
                    </button>
                    <Link className={buttonVariants({ variant: 'secondary' })} href="/cadastros-alunos/solicitar">
                        Criar cadastro de aluno
                    </Link>
                </div>
            </form>
        </Dialog>
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
            <label className="text-sm font-medium text-[#51605c]" htmlFor={id}>
                {label}
            </label>
            <input
                className="mt-1 w-full rounded-md border border-[#b9c4b7] px-3 py-2 text-sm text-[#17211f] outline-none transition focus:border-[#0f766e] focus:ring-2 focus:ring-[#d9e2d7]"
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
