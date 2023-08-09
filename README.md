## Atenção
As versões mais atuais do Kariti estão passando por atualizações e se encontram instáveis. [Clique aqui](https://github.com/karitisystem/kariti/tree/2e571d93ed7c5186439842e2b44ef48dfde94a58) para acessar a última versão estável.

# Kariti - Sistema gratuito e de códigos abertos para o suporte à correção de atividades
## Apresentação
O Kariti é um sistema web que possibilita a criação e correção de cartões-resposta para provas objetivas. Ele também cadastra provas e relaciona cada prova a uma aluno, para que o usuário tenha o mínimo trabalho para administrar suas correções.

## Instalação de depencências
### Instalando WAMP
O sistema foi projetado para funcionar no sistema operacional Windows, mas roda perfeitamente em computadores Linux. Para instalar o sistema, é preciso possuir um servidor local, para isso usaremos o [**WAMP**](https://www.google.com/search?q=WAMPSERVER&sa=X&ved=2ahUKEwiUsLbTsbjwAhVEIbkGHc_wDPoQ7xYoAHoECAEQNQ&biw=1920&bih=975). Baixe e instale o WAMP na sua máquina.
Após instalar o WAMP e coloque a pasta contendo o Kariti dentro da pasta **www**.
Agora precisamos mudar o arquivo **php.ini** contido dentro do apache do Wamp. dentro da pasta do Wamp, navegue pelo diretório **\bin\apache\apache2.4.46\bin\\** (talvez o seu não seja apache2.4.46, atente-se a isso), aqui você pode abrir com o bloco de notas o arquivo **php**, nele você vai mudar a configurção de 3 chaves, conforme mostradao a frente:
* post_max_size = 100M

* upload_max_filesize = 100M

* max_file_uploads = 90

Salve o arquivo e feche-o. Caso esse procedimento não seja feito, o número de imagens corrigidas por vez estará limitado.

* ### Instalando a máquina virtual com o Python
Antes de começar, certifique-se que há instalado na máquina que servirá como servidor, o pip e o Python 3.8.5 ou superior. Talvez sua máquina não reconheça o pip e o python como comandos externos, pra isso você precisar adicionar o path do python ao sistema, [clique aqui](https://dicasdepython.com.br/resolvido-pip-nao-e-reconhecido-como-um-comando-interno/#:~:text=Uma%20situa%C3%A7%C3%A3o%20que%20pode%20acontecer,gente%20usa%20o%20pr%C3%B3prio%20python.) para seguir um tutorial. Em seguida, dentro da pasta Kariti, crie uma pasta onde colocaremos nossa virtual env, ela se chamará **my_envs**. Abra o terminal dentro dessa pasta e instale o módulo responsável por criar a máquina virtual, usando:
~~~cmd
pip install virtualenv
~~~
Após a instalação do módulo, vamos criar a máquina virtual nessa pasta e chamá-la de **my_env_w**, para isso usaremos:
~~~cmd
virtualenv my_env_w
~~~
Agora vamos iniciar instalando as bibliotecas necessárias do python. Para isso vamos ativar a nossa máquina virtual. Com o terminal ainda no diretório **my_envs** digite:

Windows:
~~~cmd
my_env_w\Scripts\activate
~~~
Linux:
~~~cmd
source my_env_w/bin/activate
~~~
Agora vamos utilizar o pip, dentro da nossa máquina virtual, vamos instalar as bibliotecas necessárias para o funcionamento do sistema. Para isso, dentro da pasta `kariti`, basta digitar o seguinte comando:
~~~cmd
pip install -r requirements.txt
~~~


O [Composer](https://getcomposer.org/) e o [PHPMailer](https://github.com/PHPMailer/PHPMailer) são duas depedências importantes para o sistema, mas não precisam ser instaladas porque já veem implementadas.

## Configurando o sistema
Nesse momento vamos configurar o arquivo de configuração do sistema, com o notepad ou algum editor de texto abra o arquivo **settings.ini** na pasta Kariti.
A primeira sessão que iremos configurar, será a sessão **SYSTEM**. Aqui vamos modificar a chave **OS**, que será onde indicaremos para o sistema qual Sistema Operacional estamos utilizando, você pode colocar "windows" ou "linux", sem as áspas. Em seguida vamos configurar a chave **cut_path_w** (caso esteja instalando em um sistema windows) ou **cut_path_l** (caso esteja instalando em um sistema linux), aqui nós vamos colocar o caminho absoluto até a pasta do Kariti seguido de uma "/". Exemplo:
~~~
C:/wamp64/www/kariti/
~~~
Agora vamos configurar a sessão **MAIL**. A primeira chave que vamos configurar será a chave **user**, aqui você irá colocar o email responsável de enviar todos os emails. Exemplo:
~~~
seuemail@escola.com.br
~~~
A próxima chave é **password**, nessa você insere a senha do email acima. Por último, a chave **name**, nessa chave você insere o nome do remetente desses emails, você pode colocar o nome da sua instituição ou algo como "Não responder esse email". Para que o envio de email por meio dessa conta seja possível, é necessário ativar a opção de [Apps menos seguros](https://myaccount.google.com/lesssecureapps?rapt=AEjHL4NF3eTRA8O_ouFhHozSbdEjI3k7GsAKqUzai6ZsqWrfwSVU2WX6jIw-Y1f7hx893nMiBF2o-A36dUS8bHp65bzxzIQSFw). Esse é o método para o gmail. Recomenda-se a criação de um email (@gmail    ) apenas para realizar essa tarefa.
## Funções do Sistema
### Login
Na primeira tela é onde o professor irá informar suas informações para entrar no sistema. O cadastro dos professores poderá ser feito apenas pelo usuário mestre, cujo usuário padrão é **master_user** e a senha padrão é **12345678**, podendo ser modificados.

Em seguida, temos a página de **MENU**, onde temos os itens **USUÁRIO**, **ALUNO**, **TURMA** e **PROVA**.

### Usuário
Nessa sessão é possível realizar todas as operações em relação as turmas. Essa área só está disponível para o usuário mestre.
#### Cadastrar Usuário
Para cadastrar um professor como usuário bastar inserir as informações: **nome de usuário**(informação usada para acessar o login), **nome completo do professor** e **uma senha para o professor**.
#### Visualizar Usuário
Aqui estão todos os professores que o usuário master cadastrou. É possível filtar os professores pelo nome digitando parte do nome na barra de pesquisa. Também é possível editar as informações de um professor clicando no ícone de lápis do lado do professor, ou excluir o professor clicando no ícone de lata de lixo.

### Aluno
Nessa sessão é possível realizar todas as operações em relação aos estudantes.
#### Cadastrar Aluno
Para cadastrar alunos, existem dois meios:
1. **Por CSV**: Para cadastrar alunos por CSV, você pode baixar o modelo clicando em **Baixar Modelo CSV** e preencher com **Nome**, **Email** e **Número de Matrícula** do aluno, conforme o modelo e sem deletar a primeira linha (cabeçalho). Em seguida clique em **Procurar Arquivo**, selecione o arquivo que você acabou modificar e clicar em **Cadastrar CSV**.
2. **Manualmente**: Basta inserir as informações de **Nome**, **Email** e **Número de Matrícula** do aluno. Em seguida basta clicar em **Cadastrar Aluno** e o aluno será criado.
#### Visualizar Aluno
Aqui estão todos os alunos que um professor cadastrou. É possível filtar os alunos pelo nome digitando parte do nome na barra de pesquisa. Também é possível editar as informações de um aluno clicando no ícone de lápis do lado do aluno, ou excluir o aluno clicando no ícone de lata de lixo.

### Turma
Nessa sessão é possível realizar todas as operações em relação as turmas.
#### Cadastrar Turma
Para criar uma turma basta inserir as informações de **Nome da Turma**, **Nome do Curso** e **selecionar os alunos dessa turma**. Para ajudar na tarefa de selecioar os alunos, é possível fazer isso utilizando um CSV, para isso basta baixar o modelo CSV clicando em **Baixar Modelo CSV** e preencher o modelo, cada linha pode ser preenchida com **Nome**, **Email** ou **Número de Matrícula** de um aluno previamente cadastrado. Depois clique em **Procurar Arquivo** e selecione o arquivo que você acabou de editar, em seguida clique em **Cadastrar CSV** e os alunos serão selecionados.
#### Visualizar Turma
Aqui estão todas as turma que um professor cadastrou. É possível filtar as turma pelo nome digitando parte do nome na barra de pesquisa. Também é possível editar as informações de uma turma clicando no ícone de lápis do lado da turma, ou excluir a turma clicando no ícone de lata de lixo.

### Prova
Nessa sessão é possível realizar todas as operações em relação as provas.
#### Cadastrar Prova
Para criar uma prova, basta inserir as informações:
1. Nome da Prova
2. Selecionar a turma pra essa prova
3. Quantidade de questões (1 - 20)
4. Quantidade de alternativas (a - g)

Após fazer isso, clique em **GERAR PROVA**. Em seguida precisamos inserir a data da prova e as informações do gabarito. Em **Data**, clique em **dd** para alterar o dia, em **mm** para alterar o mês e **aa** para alterar o ano. Quanto ao gabarito, clique em uma das bolinhas para indicar qual a resposta correta para aquela questão. Do lado das bolinhas é possível definir o peso de cada uma das questões. Depois basta clicar em **CADASTRAR PROVA** que a prova será criada.
#### Corrigir Prova
Clicando em **Procurar Arquivos** você pode selecionar uma ou mais imagens das provas que foram preenchidas e escaneadas. Após clicar em **Enviar Arquivo** o sistema irá carregar e corrigir as provas.
#### Verificar Prova
No primeiro campo selecionamos a prova do nosso interesse, no segundo campo selecionamos se queremos a prova de apenas um aluno ou de todos os alunos relacionados àquela prova. O botão **GERAR CARTÃO RESPOSTA** gera um pdf com os cartões resposta para cada um dos alunos selecionados. O botão **VERIFICAR PROVA** leva para a tela **ANÁLISE DE PROVA**, onde é possível ter informações mais detalhadas da prova selecionada.
##### Análise de Prova
Nessa janela é possível Verificar o número de acertos e nota total na prova para cada aluno. Para os alunos selecionados é possível:
1. **Baixar Provas:** Esse botão gera um pdf com a prova dos alunos. A prova mostra a nota obtida pelo aluno e onde esse aluno acertou e errou.
2. **Baixar CSV:** Esse botão gera um CSV contendo **nome da prova**, **id do aluno**, **nome do aluno** e **nota do aluno**.
3. **Enviar por email**: Esse botão envia um email para os alunos com um resumo do desempenho dele na prova. Junto acompanha um pdf com sua prova mostrando erros e acertos.
4. **Deletar Prova:** Esse botão exclui toda a prova.
