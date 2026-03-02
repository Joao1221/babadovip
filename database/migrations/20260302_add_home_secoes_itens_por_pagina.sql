ALTER TABLE home_secoes
    ADD COLUMN IF NOT EXISTS itens_por_pagina INT NOT NULL DEFAULT 8 AFTER limite_cards;

UPDATE home_secoes
SET itens_por_pagina = limite_cards
WHERE itens_por_pagina IS NULL OR itens_por_pagina < 1;
