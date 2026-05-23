# 🏗️ Agenda Muncks - POLIFERRO

Sistema de agendamento e gestão financeira para aluguel de muncks da POLIFERRO.

## Funcionalidades

- **Agendamentos**: Cadastro, edição e visualização de agendamentos de veículos com motoristas
- **Três abas de gestão**: Agendados, Não Pagos e Concluídos
- **Cálculo financeiro**: Valor total com suporte a deslocamento e horas extras
- **Registro de pagamento**: Baixa manual com precedência do valor digitado + taxas
- **Relatório PDF**: Exportação dos registros filtrados
- **Calendário**: Visualização mensal com FullCalendar
- **Controle de conflitos**: Verificação automática de veículos já agendados no mesmo horário

## Requisitos

- PHP 8.2+
- MySQL ou SQLite
- Composer
- Node.js (para build de assets)

## Instalação

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Testes

```bash
php vendor/bin/phpunit
```

## Licença

MIT
