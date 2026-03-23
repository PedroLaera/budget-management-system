import { useEffect, useMemo, useState } from 'react';
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000',
});

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

const initialItemForm = {
    productId: '',
    quantidade: 1,
};

export default function App() {
    const [nomeCliente, setNomeCliente] = useState('');
    const [dataSolicitacao, setDataSolicitacao] = useState('');
    const [produtos, setProdutos] = useState([]);
    const [itemForm, setItemForm] = useState(initialItemForm);
    const [itens, setItens] = useState([]);
    const [carregandoProdutos, setCarregandoProdutos] = useState(true);
    const [salvando, setSalvando] = useState(false);
    const [erro, setErro] = useState('');
    const [sucesso, setSucesso] = useState('');

    useEffect(() => {
        async function loadProducts() {
            try {
                const response = await api.get('/api/products');
                const body = response.data;
                const productsData = Array.isArray(body)
                    ? body
                    : Array.isArray(body?.data)
                      ? body.data
                      : [];
                setProdutos(productsData);
            } catch {
                setErro('Não foi possível carregar os produtos.');
            } finally {
                setCarregandoProdutos(false);
            }
        }

        loadProducts();
    }, []);

    const total = useMemo(
        () => itens.reduce((acc, item) => acc + item.subtotal, 0),
        [itens]
    );

    function handleItemFieldChange(field, value) {
        setItemForm((prev) => ({
            ...prev,
            [field]: value,
        }));
    }

    function addItem() {
        setErro('');
        setSucesso('');

        const selectedProduct = produtos.find((product) => String(product.id) === String(itemForm.productId));
        const quantidade = Number(itemForm.quantidade);

        if (!selectedProduct) {
            setErro('Selecione um produto.');
            return;
        }

        if (!Number.isInteger(quantidade) || quantidade <= 0) {
            setErro('Informe uma quantidade válida.');
            return;
        }

        const valorUnitario = Number(selectedProduct.valor);
        const subtotal = valorUnitario * quantidade;

        setItens((prev) => [
            ...prev,
            {
                id: crypto.randomUUID(),
                productId: selectedProduct.id,
                nome: selectedProduct.nome,
                quantidade,
                valorUnitario,
                subtotal,
            },
        ]);

        setItemForm(initialItemForm);
    }

    function removeItem(itemId) {
        setItens((prev) => prev.filter((item) => item.id !== itemId));
    }

    async function handleSubmit(event) {
        event.preventDefault();
        setErro('');
        setSucesso('');

        if (!nomeCliente.trim()) {
            setErro('Informe o nome do cliente.');
            return;
        }

        if (!dataSolicitacao) {
            setErro('Informe a data da solicitação.');
            return;
        }

        if (itens.length === 0) {
            setErro('Adicione ao menos um item ao orçamento.');
            return;
        }

        setSalvando(true);

        try {
            const payload = {
                nome_cliente: nomeCliente.trim(),
                data_solicitacao: dataSolicitacao,
                itens: itens.map((item) => ({
                    product_id: item.productId,
                    quantidade: item.quantidade,
                })),
            };

            await api.post('/api/budgets', payload);

            setNomeCliente('');
            setDataSolicitacao('');
            setItens([]);
            setItemForm(initialItemForm);
            setSucesso('Orçamento salvo com sucesso.');
        } catch (requestError) {
            const apiMessage = requestError.response?.data?.message;
            setErro(apiMessage || 'Erro ao salvar orçamento.');
        } finally {
            setSalvando(false);
        }
    }

    return (
        <main className="page">
            <div className="card">
                <h1>Formulário de Orçamento</h1>

                <form className="form" onSubmit={handleSubmit}>
                    <div className="grid">
                        <div className="field">
                            <label htmlFor="nomeCliente">Nome do Cliente</label>
                            <input
                                id="nomeCliente"
                                type="text"
                                value={nomeCliente}
                                onChange={(event) => setNomeCliente(event.target.value)}
                                maxLength={100}
                            />
                        </div>

                        <div className="field">
                            <label htmlFor="dataSolicitacao">Data da Solicitação</label>
                            <input
                                id="dataSolicitacao"
                                type="date"
                                value={dataSolicitacao}
                                onChange={(event) => setDataSolicitacao(event.target.value)}
                            />
                        </div>
                    </div>

                    <section className="box">
                        <h2>Itens do Orçamento</h2>

                        <div className="item-grid">
                            <div className="field">
                                <label htmlFor="produto">Produto</label>
                                <select
                                    id="produto"
                                    value={itemForm.productId}
                                    onChange={(event) => handleItemFieldChange('productId', event.target.value)}
                                    disabled={carregandoProdutos}
                                >
                                    <option value="">Selecione</option>
                                    {produtos.map((product) => (
                                        <option key={product.id} value={product.id}>
                                            {product.nome} - {currencyFormatter.format(Number(product.valor))}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="field">
                                <label htmlFor="quantidade">Quantidade</label>
                                <input
                                    id="quantidade"
                                    type="number"
                                    min={1}
                                    step={1}
                                    value={itemForm.quantidade}
                                    onChange={(event) => handleItemFieldChange('quantidade', event.target.value)}
                                />
                            </div>

                            <div className="align-end">
                                <button className="btn-primary" type="button" onClick={addItem}>
                                    Adicionar
                                </button>
                            </div>
                        </div>

                        <div className="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Qtd</th>
                                        <th>Valor Unitário</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {itens.length === 0 ? (
                                        <tr>
                                            <td colSpan={5}>Nenhum item adicionado.</td>
                                        </tr>
                                    ) : (
                                        itens.map((item) => (
                                            <tr key={item.id}>
                                                <td>{item.nome}</td>
                                                <td>{item.quantidade}</td>
                                                <td>{currencyFormatter.format(item.valorUnitario)}</td>
                                                <td>{currencyFormatter.format(item.subtotal)}</td>
                                                <td className="text-right">
                                                    <button type="button" className="btn-secondary" onClick={() => removeItem(item.id)}>
                                                        Remover
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colSpan={3}>Total</td>
                                        <td>{currencyFormatter.format(total)}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>

                    {erro && <p className="alert error">{erro}</p>}
                    {sucesso && <p className="alert success">{sucesso}</p>}

                    <div className="actions">
                        <button type="submit" className="btn-success" disabled={salvando}>
                            {salvando ? 'Salvando...' : 'Salvar orçamento'}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    );
}