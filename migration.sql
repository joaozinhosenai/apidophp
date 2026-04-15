-- =============================================================
--  migration.sql  –  Criação do banco e tabela para MariaDB
--  Execute:  mysql -u root -p < migration.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS api_rest_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE api_rest_db;

CREATE TABLE IF NOT EXISTS claude (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    peso        DECIMAL(5,2)    NOT NULL COMMENT 'Peso em kg (ex: 72.50)',
    altura      DECIMAL(4,2)    NOT NULL COMMENT 'Altura em metros (ex: 1.78)',
    cor_cabelo  VARCHAR(50)     NOT NULL COMMENT 'Ex: castanho, preto, loiro',
    cor_olho    VARCHAR(50)     NOT NULL COMMENT 'Ex: castanho, verde, azul',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Perfil físico – rota /claude';

-- ------------------------------------------------------------------
--  Seeds de exemplo para testar no Postman
-- ------------------------------------------------------------------
INSERT INTO claude (peso, altura, cor_cabelo, cor_olho) VALUES
    (72.50, 1.78, 'castanho',  'castanho'),
    (65.00, 1.65, 'preto',     'verde'),
    (80.30, 1.82, 'loiro',     'azul'),
    (58.90, 1.60, 'ruivo',     'castanho-claro');