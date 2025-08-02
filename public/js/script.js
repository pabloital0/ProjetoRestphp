async function carregarUsuarios() {
  try {
    const resposta = await fetch('../api/usuarios.php');
    const usuarios = await resposta.json();

    const lista = document.getElementById('lista-usuarios');
    lista.innerHTML = '';

    usuarios.forEach(usuario => {
      const li = document.createElement('li');
      li.textContent = `${usuario.id}: ${usuario.nome} `;

      const botaoExcluir = document.createElement('button');
      botaoExcluir.textContent = 'Excluir';
      botaoExcluir.onclick = () => excluirUsuario(usuario.id);

      const botaoEditar = document.createElement('button');
      botaoEditar.textContent = 'Editar';
      botaoEditar.onclick = () => editarUsuario(usuario.id, usuario.nome);

      li.appendChild(botaoExcluir);
      li.appendChild(botaoEditar);
      lista.appendChild(li);
    });

  } catch (erro) {
    console.error('Erro ao carregar usuários:', erro);
  }
}


async function adicionarUsuario() {
  const nome = document.getElementById('nome').value;

  if (!nome.trim()) {
    alert('Digite um nome');
    return;
  }

  try {
    await fetch('../api/usuarios.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ nome })
    });

    document.getElementById('nome').value = '';
    carregarUsuarios(); // Atualiza a lista
  } catch (erro) {
    console.error('Erro ao adicionar usuário:', erro);
  }
}



async function excluirUsuario(id) {
  if (!confirm("Tem certeza que deseja excluir este usuário?")) return;

  try {
    await fetch('../api/usuarios.php', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `id=${id}`
    });

    carregarUsuarios(); // Atualiza lista
  } catch (erro) {
    console.error('Erro ao excluir usuário:', erro);
  }
}

async function editarUsuario(id, nomeAtual) {
  const novoNome = prompt("Digite o novo nome:", nomeAtual);
  if (!novoNome || !novoNome.trim()) return;

  try {
    await fetch('../api/usuarios.php', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id, nome: novoNome })
    });

    carregarUsuarios(); // Atualiza a lista
  } catch (erro) {
    console.error('Erro ao editar usuário:', erro);
  }
}
