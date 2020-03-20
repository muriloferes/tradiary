CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE usuario (
    idusuario UUID DEFAULT uuid_generate_v4(),
    dthrcriacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    nome VARCHAR(50) NOT NULL,
    senha VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ativo',
    CONSTRAINT pk_usuario PRIMARY KEY (idusuario),
    CONSTRAINT un_usuario_nome UNIQUE (nome)
);

CREATE TABLE operacao (
    idoperacao UUID DEFAULT uuid_generate_v4(),
    dthrcriacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idusuario UUID NOT NULL,
    dtoperacao DATE NOT NULL,
    totalbruto NUMERIC(12, 2) NOT NULL DEFAULT 0,
    totalliquido NUMERIC(12, 2) NOT NULL DEFAULT 0,
    contratos INTEGER NOT NULL DEFAULT 0,
    CONSTRAINT pk_operacao PRIMARY KEY (idoperacao),
    CONSTRAINT fk_operacao_usuario FOREIGN KEY (idusuario) REFERENCES usuario (idusuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT un_operacao_idusuario_dtoperacao UNIQUE (idusuario, dtoperacao)
);

CREATE TABLE deposito (
    iddeposito UUID DEFAULT uuid_generate_v4(),
    dthrcriacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idusuario UUID NOT NULL,
    dtdeposito DATE NOT NULL,
    valor NUMERIC(12, 2) NOT NULL,
    CONSTRAINT pk_deposito PRIMARY KEY (iddeposito),
    CONSTRAINT fk_deposito_usuario FOREIGN KEY (idusuario) REFERENCES usuario (idusuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT un_deposito_idusuario_dtdeposito UNIQUE (idusuario, dtdeposito)
);

CREATE TABLE retirada (
    idretirada UUID DEFAULT uuid_generate_v4(),
    dthrcriacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idusuario UUID NOT NULL,
    dtretirada DATE NOT NULL,
    valor NUMERIC(12, 2) NOT NULL,
    CONSTRAINT pk_retirada PRIMARY KEY (idretirada),
    CONSTRAINT fk_retirada_usuario FOREIGN KEY (idusuario) REFERENCES usuario (idusuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT un_retirada_idusuario_dtretirada UNIQUE (idusuario, dtretirada)
);