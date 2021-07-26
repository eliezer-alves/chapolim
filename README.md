<p align="center">
  <a href="https://nolock.com.br/" target="blank"><img src="https://i.imgur.com/2UgyvkB.png" width="300" alt="NOLOCK" /></a>
</p>
    
# Chapolim
Este projeto tem como objetivo fornecer alguns comandos adicionais à interface de linha de comando do Laravel, o Artisan, para manipular a estrutura das camadas de Serviço e Reopositório: Service Layer / Repository Pattern.

## Sumário
<ol style="list-style: none"  style="color: black">
  <li><a href="#laravel"><b><span style="color: red">#</span> Laravel</a></b></li>
  <li><a href="#artisan"><b><span style="color: red">#</span> Artisan</a></b></li>
  <li><a href="#reposittory-pattern"><b><span style="color: red">#</span> Reposittory Pattern</a></b></li>
  <li><a href="#service-layer"><b><span style="color: red">#</span> Service Layer</a></b></li>
  <li><a href="#setup"><b><span style="color: red">#</span> Setup</a></b></li>  
  <li>
    <a href="#uso"><b><span style="color: red">#</span> Uso</b></a>
    <ul style="list-style: none">
      <li><a href="#chapolimrepository"><span style="color: red">#</span> chapolim:make-repository</a></li>
      <li><a href="#chapolimservice"><span style="color: red">#</span> chapolim:make-service</a></li>
      <li><a href="#chapolimmodel"><span style="color: red">#</span> chapolim:make-model</a></li>
      <li><a href="#chapolimcontroller"><span style="color: red">#</span> chapolim:make-controller</a></li>
      <li><a href="#chapolimmake"><span style="color: red">#</span> chapolim:make</a></li>
    </ul>
  </li>
</ol>

## Laravel
<a href="#sumário">Sumário</a><br/>
<i>Laravel é um framework de aplicação web com sintaxe expressiva e elegante. Uma estrutura da web fornece uma estrutura e um ponto de partida para a criação de seu aplicativo, permitindo que você se concentre na criação de algo incrível enquanto suamos nos detalhes.</i> <a href="https://laravel.com/docs/8.x#meet-laravel">Documentação do Laravel</a>

## Artisan
<a href="#sumário">Sumário</a><br/>
<i>"Artisan é a interface de linha de comando incluída no Laravel. O Artisan existe na raiz do seu aplicativo como o artisanscript e fornece uma série de comandos úteis que podem ajudá-lo enquanto você constrói seu aplicativo."</i> <a href="https://laravel.com/docs/8.x/artisan#introduction">Documentação do Laravel</a>

Para ver uma lista de todos os comandos Artisan disponíveis, você pode usar o listcomando:

    php artisan list

## Reposittory Pattern
<a href="#sumário">Sumário</a><br/>
<a href="https://asperbrothers.com/blog/implement-repository-pattern-in-laravel/">Reposittory Pattern</a> é um padrão de projeto que visa adicionar uma camada de abstração entre a camada dos modelos (Models) e os controladores (Controllers) ou ainda da camada de serviço (Services). Dessa forma, cada Model possui uma classe Repository correspondente. Ademais, numa abordagem padrão essas classes ficam na pasta app/Repositories/Eloquent e são injetadas por meio de Interfaces, que se encontram em app/Repositories/Contracts.

## Service Layer
<a href="#sumário">Sumário</a><br/>
A Camada de Serviço ou <a href="https://luis-barros-nobrega.medium.com/laravel-service-layer-with-dtos-and-validators-2c6303899a57">Service Layer</a> é um padrão de projeto que visa abstrair a lógica ou regra de nogócio da aplicação, que normalmente se encontra na camado dos controladores, para uma nova camada: a Camada de Serviço. Nesse contexto, em uma abordagem padrão cada controlador possui sua classe de serviço para quem delega as funções que normalmente deveria exercer. Dessa forma, os controladores se limitam a gerenciar o que entra por meio das rotas (requests) e o que será retornado a partir de então (responses). Assim, além de o projeto ficar mais estruturado é garantido também um desacoplamento da regra de negócio e o framework em uso, pois a regra de negócio estará em uma camada criada exclusivamente pelo desenvolvedor.

## Setup
<a href="#sumário">Sumário</a><br/>
Para utilizar esse <i>litle package</i> desenvolvido por um <i>pocket edition developer</i> basta instalar o pacote via composer, para isso execute o seguinte comando na raiz do seu projeto:

    composer require eliezer/chapolim

## Uso
<a href="#sumário">Sumário</a><br/>
Uma vez devidamente instalado você terá acesso aos comandos `php artisan chapolim ---`, esses comandos serão listados juntamente com os outros comandos do Artisan ao exceutar:
    
    php artisan

Os comandos `chapolim` serão listados da seguinte forma:

    ...
    chapolim
      chapolim:make        Generates project scaffold with all layers
      chapolim:make-controller  Create a new controller class with service layer
      chapolim:make-model       Create a new model class with table attribute and fillable attribute
      chapolim:make-repository  Create a new repository class
      chapolim:make-service     Create a new service class
    ...
  
  Dessa forma segue a descrição de cada comando.

  ### chapolim:make-repository
  <a href="#sumário">Sumário</a><br/>
  As classes geradas como o comando `chapolim:make-repository` serão criadas no diretório `app/Repositories/Eloquent`, esse diretório não existe por padrão, dessa forma ele será criado a primeira vez que for rodado o comando. Ainda assim, um outro diretório será criado `app/Repositories/Contracts`, esse diretório conterá as classes de interface das classes de repositório, pois estas nunca são injetadas diretamente.
  Ademais, a primeira vez o o comando `chapolim:make-repository` for rodado será feito um processo de scafolding onde, além de serem criados os diretórios supracitados, serão criadas as classes `AbstractRepository` (contendo todos os métodos padrões de uma classe de repositório a qual será estendida por todas as outras classes de repositório), a sua interface `AbstractRepositoryInterface` e finalmente a classe `RepositoryServiceProvider`, essa última será a classe responsável por informar à aplicação a relação entre as classes de repositório e suas interfaces, sendo que é por conta disso que será possível utilizar as classes por meio das suas interfaces.
  Assim, toda vez que for criada uma nova classe de repositório será também criada a sua interfece e a relação entre as duas será provida à aplicação por meio da `RepositoryServiceProvider`, sendo que isto é feito varrendo o diretório `app/Repositories/Eloquent` e reescrevendo o arquivo com as classes presentes nesse diretório. Ademais, é importante frizar que a classe `RepositoryServiceProvider` é automaticamente inserida em `config/app.php` mas pode ser que isso não aconteça então é importante validar se ela se encontra no <i>array</i> de <i>providers</i>.

  Segue os detalhes do comando:

  <b>Descrição:</b>
  Cria uma nova classe repositório

  <b>Formato:</b>
  chapolim:make-repository [options] [--] <name>

  <b>Argumentos:</b>
    <p><i>name</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;O nome da Classe</p>

  <b>Options:</b>
    <p><i>-m, --model[=Model]</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Gera uma classe de repositório para uma <i>Model</i> fornecida.</p>
    <p><i>-p, --path[=Path]</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;Gera uma classe de repositório em um diretório fornecido a partir de app/Repositories.</p>

  ### chapolim:make-service
  <a href="#sumário">Sumário</a><br/>
  As classes geradas como o comando `chapolim:make-service` serão criadas no diretório `app/Services`, esse diretório não existe por padrão, dessa forma ele será criado a primeira vez que for rodado o comando.
  Segue os detalhes do comando:

  <b>Descrição:</b>
  Cria uma nova classe de serviço

  <b>Formato:</b>
  chapolim:make-service [options] [--] <name>

  <b>Argumentos:</b>
    <p><i>name</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;O nome da Classe</p>

  <b>Options:</b>
    <p><i>-r, --resource</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;Gera uma classe de serviço com métodos padrões.</p>
    <p><i>-R, --repository[=Repository]</i>&emsp;&emsp;&emsp;Gera uma classe de serviço injetando uma classe de repositório.</p>

  ### chapolim:make-model
  <a href="#sumário">Sumário</a><br/>
  Por padrão o Artisan já possui comandos para a criação dos modelos, no entanto nesses comandos não é possível a especificação do <i>fillable</i> da classe, ou seja, não é possível especificar as colunas da tabela cujo o modelo representa. Dessa forma, o chapolim possui um comando para gerar os modelos com essa propriedade: `chapolim:make-model --fillable='username|email|password'`. Dessa maneira, como você pode notar você pode especificar seu <i>fillable</i> por meio da <i>option</i> `--fillable`.
  Segue os detalhes do comando:

  <b>Descrição:</b>
  Cria uma nova classe de modelo

  <b>Formato:</b>
  chapolim:make-model [options] [--] <name>

  <b>Argumentos:</b>
    <p><i>name</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;O nome da Classe</p>

  <b>Options:</b>
    <p><i>-F, --fillable[='column|column2']</i>&emsp;&nbsp;Gera uma classe de modelo com o atributo <i>fillable</i> preenchido.</p>

  ### chapolim:make-controller
  <a href="#sumário">Sumário</a><br/>
  Da mesma forma que para os modelos, o Artisan já possui comandos para a criação dos controladores, entretanto não existe nesses comandos a opção de injetar uma classe de serviço. Dessa forma, o chapolim possui um comando para gerar controladores com classes de serviço: `chapolim:make-controller`. Assim, as classes geradas como o comando `chapolim:make-controller` serão criadas no diretório `app/Http/Controllers` e já virão com uma classe de serviço injetada, sendo que o nome desta pode ser especificado por meio da <i>option</i> `--service`. Nesse contexto, caso a classe de serviço não for especificada será injetada uma seguindo o pdrão do nome do controlador. Ademais, é possível gerar automáticamente um grupo de rotas do controlador em `app/routes/api.php` sendo que para isto basta especificar a <i>option</i> `--route`.
  Segue os detalhes do comando:

  <b>Descrição:</b>
  Cria uma nova classe de controlador injetando uma classe de serviço

  <b>Formato:</b>
  chapolim:make-controller [options] [--] <name>

  <b>Argumentos:</b>
    <p><i>name</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;O nome da Classe</p>

  <b>Options:</b>    
    <p><i>-S, --service[=Service]</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Gera uma classe de controlador injetando uma classe de serviço específica.</p>
    <p><i>-r, --resource</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;Gera uma classe de serviço com métodos padrões.</p>
    <p><i>--route</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;Gera um grupo de rotas para o controlador em `app/routes/api.php`.</p>


  ### chapolim:make
  <a href="#sumário">Sumário</a><br/>
  Finalmente chegamos ao principal comando do pacote: `chapolim:make`, com este comando é possível criar uma estrutura completa envolvendo todas as quatros camadas: `Model`, `Repoditory`, `Service` e `Controller`. Nesse, contexto o comando irá receber o argumento <i>`name`</i> que será a base para a geração das classes especificadas sendo que essas classes serão especificadas por meio das <i>`options`</i>. Assim, o chapolim irá simplismente executar os comandos já existentes para a criação das classes de cada camada. Dessa forma, vale ressaltar que se por acaso as <i>options</i> referentes às camadas não forem informadas será o mesmo que passar a <i>option</i> `--all`, ou seja, o chapolim irá criar as classes de todas as camadas. Dessa maneira o comando 
    `php artisan chapolim:make Caneta --fillable='cor|marca|preco'  --route -r`
  irá criar uma estrutura completa para o CRUD padrão de `Canetas`.
  Segue os detalhes do comando:

  <b>Descrição:</b>
  Cria uma nova classe de controlador injetando uma classe de serviço

  <b>Formato:</b>
  chapolim:make [options] [--] <name>

  <b>Argumentos:</b>
    <p><i>name</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;O nome base para as classes</p>

  <b>Options:</b>
    <p><i>-m, --model</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Gera uma classe Model a partir do <i>name</i>.</p>
    <p><i>-c, --controller</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Gera uma classe Controller a partir do <i>name</i>.</p>
    <p><i>-R, --repository</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;&nbsp;Gera uma classe Repository a partir do <i>name</i>.</p>
    <p><i>-S, --service</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;Gera uma classe Service a partir do <i>name</i>.</p>
    <p><i>-a, --all</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Gera as classes de todas as camadas a partir do <i>name</i>.</p>
    <p><i>-r, --resource</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;Gera as classes Controller e Service (caso especificadas nas options) com métodos padrões.</p>
    <p><i>--route</i>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;Gera um grupo de rotas para o controlador em `app/routes/api.php`.</p>
    <p><i>-F, --fillable[='column|column2']</i>&emsp;&nbsp;Gera a classe de modelo com o atributo <i>fillable</i> preenchido.</p>
