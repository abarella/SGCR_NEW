-- Script para criar a tabela T0111_ESCALA_SEMANAL
-- Execute este script no SQL Server Management Studio

USE sgcr;
GO

-- Verificar se a tabela já existe
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'crsa' AND TABLE_NAME = 'T0111_ESCALA_SEMANAL')
BEGIN
    -- Criar a tabela
    CREATE TABLE crsa.T0111_ESCALA_SEMANAL (
        id INT IDENTITY(1,1) PRIMARY KEY,
        lotes VARCHAR(255) NOT NULL,
        produto VARCHAR(255) NOT NULL,
        dat_inicial DATETIME NOT NULL,
        dat_final DATETIME NOT NULL,
        dat_exec DATETIME NULL,
        id_tarefa INT NOT NULL,
        nom_responsaveis TEXT NOT NULL,
        id_tipoprocesso INT NOT NULL,
        cdusuario INT NOT NULL,
        datatualizacao DATETIME NOT NULL DEFAULT GETDATE()
    );
    
    PRINT 'Tabela T0111_ESCALA_SEMANAL criada com sucesso!';
    
    -- Criar índices para melhor performance
    CREATE INDEX IX_ESCALA_SEMANAL_LOTES ON crsa.T0111_ESCALA_SEMANAL (lotes);
    CREATE INDEX IX_ESCALA_SEMANAL_DATA_INICIAL ON crsa.T0111_ESCALA_SEMANAL (dat_inicial);
    CREATE INDEX IX_ESCALA_SEMANAL_TAREFA ON crsa.T0111_ESCALA_SEMANAL (id_tarefa);
    CREATE INDEX IX_ESCALA_SEMANAL_TIPO_PROCESSO ON crsa.T0111_ESCALA_SEMANAL (id_tipoprocesso);
    
    PRINT 'Índices criados com sucesso!';
END
ELSE
BEGIN
    PRINT 'Tabela T0111_ESCALA_SEMANAL já existe!';
END

-- Verificar a estrutura da tabela
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'crsa' 
AND TABLE_NAME = 'T0111_ESCALA_SEMANAL' 
ORDER BY ORDINAL_POSITION;

-- Contar registros na tabela
SELECT COUNT(*) as TotalRegistros FROM crsa.T0111_ESCALA_SEMANAL;
