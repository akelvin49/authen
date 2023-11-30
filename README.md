![GitHub repo size](https://img.shields.io/github/repo-size/akelvin49/authen)
[<img src = "https://img.shields.io/badge/akelvinlima-%23E4405F.svg?&style=flat-square&logo=instagram&logoColor=white">](https://www.instagram.com/akelvinlima/)
[<img src="https://img.shields.io/badge/Kelvin_Lima-%230077B5.svg?&style=flat-square&logo=linkedin&logoColor=white" />](https://www.linkedin.com/in/kelvin-lima-89074a1ab/)
[![Source Code](http://img.shields.io/badge/source-akelvin49/authen-blue.svg?style=flat-square)](https://github.com/akelvin49/authen)
![GitHub language count](https://img.shields.io/github/languages/count/akelvin49/authen)
![GitHub top language](https://img.shields.io/github/languages/top/akelvin49/authen)

# Projeto A U T H E N

## :pushpin: Sobre

### O AUTHEN constitui-se como um componente de autenticação em PHP, proporcionando uma ágil inicialização para projetos futuros. Baseado no projeto authphp desenvolvido em [Código Aberto do Robson .V Leite](https://www.youtube.com/playlist?list=PLi_gvjv-JgXpyYOJA-8TDQ0BLLugiX4jO).

## :mag_right: Recursos Inclusos

- Rotas - componente [Router - Coffecode](https://packagist.org/packages/coffeecode/router#1.0.8)
- Camada de dados - componente [Datalayer - Coffecode](https://packagist.org/packages/coffeecode/datalayer)
- SEO - componente [Optimizer - Coffecode](https://packagist.org/packages/coffeecode/optimizer)
- EMAIL - componente [PHPMailer](https://packagist.org/packages/phpmailer/phpmailer)
- Autenticação Social - componentes [oauth2-facebook - League](https://packagist.org/packages/league/oauth2-facebook) e [oauth2-google - League](https://packagist.org/packages/league/oauth2-google)
- Template php nativo [plates - League](https://packagist.org/packages/league/plates)
- Minificação CSS e JS [Minify - Matthiasmullie](https://packagist.org/packages/matthiasmullie/minify)

---

## :computer: Requisitos

- PHP>=7.4
- JQUERY
- COMPOSER
- MYSQL
- [APP do Facebook](https://developers.facebook.com/?no_redirect=1) e [APP do Google](https://console.cloud.google.com/apis/dashboard) para login social

---

## :hammer: **Instalação**

Efetue o clone do repositório

```
git clone https://github.com/akelvin49/authen.git
```

A seguir, instale o composer na raiz do projeto EX: 'authen/', caso ja o tenha pule esta estapa.
Ajuda para instalação do composer em https://getcomposer.org/download/

Após o composer, deve-se instalar as dependências do projeto.

```
composer install
```

**Aguarde conclusão...**

Verifique o diretório vendor:

- Após a instalação, um diretório chamado 'vendor' será criado dentro do diretório 'source/' no seu projeto. Ele contém todas as dependências instaladas.

## :pencil2: **Configurações Básicas**

Como o authen é um componente de login, se faz necessário a instalação de um banco de dados.
No diretório 'source/' contém um banco de dados de usuário básico, 'authen.sql'. Lembre-se de importá-lo.

Após isto, crie um arquivo 'env.ini' tendo o 'env.sample.ini' como base e configure-os para seu projeto.

Uma configuração basica também se faz necessária em config.php.
Em ambos os arquivos á exemplos das configurações
