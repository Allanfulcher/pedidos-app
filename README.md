# Controle de Vendas - Mercado Du Povão

![Logo](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSgw1CpkBPIXeB5-TkiOJWcWwye_YkJnX6ywg&s)

Controle de Vendas é uma aplicação web desenvolvida para gerenciar as operações de vendas do **Mercado Du Povão**. Com uma interface intuitiva e funcionalidades robustas, a aplicação facilita o gerenciamento de clientes, produtos, pedidos e relatórios, proporcionando uma visão geral eficiente do desempenho do negócio.

## 🚀 Recursos

- **Autenticação de Usuários:**
  - Registro de novos usuários.
  - Login seguro com autenticação Firebase.
  - Redefinição de senha via e-mail.

- **Gerenciamento de Clientes:**
  - Adição, edição e exclusão de clientes.
  - Listagem dinâmica de clientes com status ativo/inativo.

- **Gerenciamento de Produtos:**
  - Adição, edição e exclusão de produtos.
  - Controle de estoque com alertas para baixos níveis.

- **Gestão de Pedidos:**
  - Criação e edição de pedidos.
  - Adição de múltiplos itens por pedido.
  - Atualização automática de estoque.
  - Visualização detalhada e impressão de pedidos.
  - Cancelamento de pedidos com reversão de estoque.

- **Dashboard Informativo:**
  - Visão geral de vendas, pedidos pendentes e estoque total.
  - Gráficos interativos de vendas mensais utilizando Chart.js.

- **Configurações do Usuário:**
  - Alteração de e-mail e senha.
  - Configurações do sistema, incluindo moeda e notificações.

- **Relatórios:**
  - Geração de relatórios personalizados com filtros de data.
  - Análise de clientes mais ativos e produtos mais vendidos.

## 🛠 Tecnologias Utilizadas

- **Front-end:**
  - HTML5 & CSS3
  - [Bootstrap 5](https://getbootstrap.com/) para design responsivo.
  - [SweetAlert2](https://sweetalert2.github.io/) para alertas estilizados.
  - [FontAwesome](https://fontawesome.com/) para ícones.
  - [Chart.js](https://www.chartjs.org/) para gráficos interativos.

- **Back-end:**
  - PHP para a lógica do servidor.
  - [Firebase Authentication](https://firebase.google.com/products/auth) para gerenciamento de usuários.
  - [Firebase Firestore](https://firebase.google.com/products/firestore) como banco de dados NoSQL.

## 📦 Instalação

### Pré-requisitos

- Servidor web com suporte a PHP (ex: Apache, Nginx).
- Conta no [Firebase](https://firebase.google.com/) com um projeto configurado.
- Composer (opcional, caso queira gerenciar dependências PHP).

### Passo a Passo

1. **Clone o Repositório:**

   ```bash
   git clone https://github.com/seu-usuario/controle-de-vendas.git
   ```

2. **Configurar o Firebase:**

   - No console do Firebase, crie um novo projeto ou utilize um existente.
   - Ative a autenticação por e-mail/senha.
   - Configure o Firestore com as regras de segurança apropriadas.
   - Obtenha as credenciais do Firebase e atualize o arquivo `firebase_config.php` com suas próprias configurações:

   ```php
   // firebase_config.php
   <script>
   // Firebase Configuration
   const firebaseConfig = {
       apiKey: "SUA_API_KEY",
       authDomain: "SEU_AUTH_DOMAIN",
       projectId: "SEU_PROJECT_ID",
       storageBucket: "SEU_STORAGE_BUCKET",
       messagingSenderId: "SEU_MESSAGING_SENDER_ID",
       appId: "SEU_APP_ID"
   };

   firebase.initializeApp(firebaseConfig);
   const auth = firebase.auth();
   const db = firebase.firestore();
   </script>
   ```

3. **Configurar o Servidor Web:**

   - Coloque os arquivos clonados no diretório raiz do seu servidor web.
   - Assegure-se de que o servidor está configurado para processar arquivos PHP.

4. **Acessar a Aplicação:**

   - Abra o navegador e navegue até `http://seu-dominio.com/index.php` para acessar a página de login.
   - Registre um novo usuário ou faça login com credenciais existentes.

## 📖 Estrutura do Projeto

- `index.php`: Página de login.
- `register.php`: Página de registro de novos usuários.
- `forgot-password.php`: Página para redefinição de senha.
- `dashboard.php`: Página principal com visão geral e gráficos.
- `clientes.php`: Gerenciamento de clientes.
- `produtos.php`: Gerenciamento de produtos.
- `pedidos.php`: Gerenciamento de pedidos.
- `configuracoes.php`: Configurações do usuário e do sistema.
- `relatorios.php`: Geração de relatórios.
- `header.php`: Cabeçalho comum a todas as páginas.
- `nav.php`: Barra de navegação.
- `footer.php`: Rodapé comum a todas as páginas.
- `firebase_config.php`: Configuração do Firebase.
- `readme.md`: Este arquivo.

## 📂 Banco de Dados

A aplicação utiliza o Firebase Firestore como banco de dados NoSQL. A estrutura básica das coleções é a seguinte:

- **users**
  - `uid` (documento)
    - `email`: String
    - `createdAt`: Timestamp

- **clientes**
  - `clienteId` (documento)
    - `nome`: String
    - `email`: String
    - `telefone`: String
    - `status`: String (ativo ou inativo)
    - `createdAt`: Timestamp

- **produtos**
  - `produtoId` (documento)
    - `nome`: String
    - `preco`: Number
    - `quantidade`: Number
    - `createdAt`: Timestamp

- **pedidos**
  - `pedidoId` (documento)
    - `clienteId`: Referência para clientes
    - `itens`: Array de objetos
    - `produtoId`: Referência para produtos
    - `nome`: String
    - `preco`: Number
    - `quantidade`: Number
    - `total`: Number
    - `status`: String (pendente, pago, cancelado)
    - `data`: Timestamp

## 🧑‍💻 Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests para melhorias e novas funcionalidades.

1. **Fork o Repositório**
2. **Crie uma Branch para sua feature** (`git checkout -b feature/NovaFeature`)
3. **Commit suas Mudanças** (`git commit -m 'Adiciona nova feature'`)
4. **Push para a Branch** (`git push origin feature/NovaFeature`)
5. **Abra um Pull Request**

## 📝 Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 📞 Contato

Para mais informações ou suporte, entre em contato:

- **Nome:** Allan
- **Email:** allanfulcher@gmail.com
- **Telefone:** +5554993264627
