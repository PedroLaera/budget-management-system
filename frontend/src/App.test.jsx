import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import App from './App';

jest.mock('axios', () => {
  const mockGet = jest.fn();
  const mockPost = jest.fn();

  return {
    create: () => ({ get: mockGet, post: mockPost }),
    __mockGet: mockGet,
    __mockPost: mockPost,
  };
});

import axios from 'axios';
const mockGet = axios.__mockGet;
const mockPost = axios.__mockPost;

describe('App', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('carrega produtos e adiciona item', async () => {
    mockGet.mockResolvedValue({
      data: [{ id: 1, nome: 'Mouse', valor: '150.00' }],
    });

    mockPost.mockResolvedValue({ data: {} });

    render(<App />);

    await waitFor(() => {
      expect(screen.getByText(/Mouse/i)).toBeInTheDocument();
    });

    await userEvent.selectOptions(screen.getByLabelText(/Produto/i), '1');
    await userEvent.clear(screen.getByLabelText(/Quantidade/i));
    await userEvent.type(screen.getByLabelText(/Quantidade/i), '2');
    await userEvent.click(screen.getByRole('button', { name: /Adicionar/i }));

    // 👇 corrigido: R$ 300,00 aparece 2x (subtotal + total)
    expect(screen.getAllByText(/R\$\s*300,00/i)).toHaveLength(2);
  });
});