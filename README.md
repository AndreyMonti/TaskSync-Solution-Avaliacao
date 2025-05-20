# TaskSync-Solution

Site de gerenciamento de Tasks (tarefas) em PHP, que permite cadastrar usuários, efetuar login e realizar operações de CRUD em Tasks (criar, editar, excluir).

## Índice

- [Pré-requisitos](#pré-requisitos)  
- [Passo a passo de instalação](#passo-a-passo-de-instalação)  
  - [1. Baixar o arquivo SQL do banco “tasksynch”](#1-baixar-o-arquivo-sql-do-banco-tasksynch)  
  - [2. Criar e importar o banco no phpMyAdmin](#2-criar-e-importar-o-banco-no-phpmyadmin)  
  - [3. Clonar este repositório](#3-clonar-este-repositório)  
  - [4. Configurar conexão com o banco de dados](#4-configurar-conexão-com-o-banco-de-dados)  
- [Como usar o site](#como-usar-o-site)  
  - [1. Cadastro de usuário](#1-cadastro-de-usuário)  
  - [2. Login](#2-login)  
  - [3. Publicar nova Task](#3-publicar-nova-task)  
  - [4. Editar Task existente](#4-editar-task-existente)  
  - [5. Excluir Task existente](#5-excluir-task-existente)  
- [Problemas comuns](#problemas-comuns)  
- [Estrutura de pastas](#estrutura-de-pastas)  
- [Licença](#licença)

---

## Pré-requisitos

Antes de começar, certifique-se de ter em sua máquina:

- Servidor web com PHP 7.x ou superior (XAMPP, WAMP, MAMP, LAMP, etc.).  
- phpMyAdmin (já vem incluso no XAMPP/WAMP/MAMP).  
- Git (para clonar o repositório).  
- Um navegador (Chrome, Firefox, Edge, etc.).  

---

## Passo a passo de instalação

### 1. Baixar o arquivo SQL do banco “tasksynch”

1. Localize o arquivo `tasksynch.sql` (fornecido junto a este projeto).  
2. Caso você ainda não possua esse arquivo, peça ao responsável pelo projeto ou faça download do diretório `database/` deste repositório (se existir).  

### 2. Criar e importar o banco no phpMyAdmin

1. Abra o phpMyAdmin no navegador, por exemplo:  
2. Clique em **“Novo”** (ou “New”) para criar um banco de dados vazio.  
3. Defina o nome do banco como: tasksynch
e selecione o _collation_ `utf8mb4_general_ci`.  
4. Clique em **“Criar”**.  
5. Com o banco `tasksynch` selecionado, clique na aba **“Importar”** (ou “Import”).  
6. Clique em **“Escolher arquivo”**, selecione `tasksynch.sql` e clique em **“Executar”**.  
7. Aguarde a mensagem de sucesso. Todas as tabelas e dados iniciais serão importados corretamente.

### 3. Clonar este repositório

1. Abra o terminal (macOS/Linux) ou Git Bash (Windows).  
2. Navegue até a pasta de seu servidor web local. Exemplos:  
- **Windows + XAMPP**:
  ```bash
  cd C:/xampp/htdocs/
  ```  
- **Linux + LAMP**:
  ```bash
  cd /var/www/html/
  ```  
3. Clone este repositório com o comando:
```bash
git clone https://github.com/AndreyMonti/TaskSynch-Solution-Avaliacao.git
