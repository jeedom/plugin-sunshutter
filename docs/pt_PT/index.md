# Plug-in de Gerenciamento de Obturador

# Description

Este plugin torna mais fácil gerenciar a posição de suas venezianas de acordo com a posição do sol. Este plugin funciona totalmente localmente e não requer uma conexão externa.

Você pode encontrar [aqui](https://www.jeedom.com/blog/?p=4310) um artigo mostrando um exemplo de configuração do plug-in.

# Configuração de plugins

Nada de especial aqui apenas para instalar e ativar o plugin.

## Como funciona ?

O plug-in ajustará a posição das persianas em relação às posições do sol (azimute e altitude), dependendo da condição.

# Configuração das persianas

A configuração é dividida em várias guias :

## Equipement

Você encontrará na primeira aba toda a configuração do seu equipamento :

- **Nome de equipamentos** : nome do seu equipamento.
- **Objeto pai** : indica o objeto pai ao qual o equipamento pertence.
- **Categoria** : permite que você escolha a categoria do seu equipamento.
- **Ativar** : torna seu equipamento ativo.
- **Visivél** : torna seu equipamento visível no painel.

## Configuration

### Configuration

- **Verificação** : frequência de verificação das condições e posição dos flaps.
- **Recuperar o controle** : proíbe que o sistema de gerenciamento de obturador mude de posição se tiver sido movido manualmente. Exemplo : o sistema fecha o obturador, você o abre, ele não será mais tocado até que o comando "Retomar gerenciamento" seja acionado ou se o tempo para assumir o controle tiver passado.
- **Latitude** : a latitude do seu obturador / casa.
- **Longitude** : a longitude do seu obturador / casa.
- **Altitude** : a altura do seu obturador / casa.
- **Estado do obturador** : comando indicando a posição atual do obturador.
- **Posição do obturador** : controle para posicionar a aba.
- **Atualizar posição do obturador (opcional)** : comando para atualizar a posição do obturador.
- **Tempo máximo para uma viagem** : tempo para fazer um movimento completo (para cima e para baixo ou para cima e para baixo) em segundos.

## Condition

- **Condição para ação** : se esta condição não for verdadeira, o plugin não irá modificar a posição do painel.
- **A alteração de modo cancela suspensões pendentes** : se marcada, uma mudança de modo do obturador retorna ao gerenciamento automático.
- **Ações imediatas são sistemáticas e prioritárias** : se marcado, então as ações imediatas são executadas mesmo se estiver suspenso e sem levar em consideração a ordem das condições.

A tabela de condições permite especificar condições de posicionamento específicas, que são mantidas na tabela de posição da aba :
- **Posição** : se a condição for verdadeira, a posição do obturador.
- **Modo** : a condição só funciona se o obturador estiver neste modo (você pode colocar vários separados por vírgulas ``,``). Se este campo não for preenchido, a condição será testada em qualquer modo.

>**IMPORTANTE**
>
>Estamos falando sobre o modo de obturador aqui, não tem nada a ver com o plugin de modo

- **Ação imediata** : actua imediatamente assim que a condição for verdadeira (por isso não espera pelo cron de verificação).
- **Suspender** : se a condição for verdadeira, ele suspende o gerenciamento automático do obturador.
- **Condição** : sua condição.
- **COMMENTAIRE** : campos livres para comentários.

## Positionnement

- **% abertura** : a% quando o obturador está aberto.
- **% de fechamento** : a% quando o obturador é fechado.
- **Ação padrão** : a ação padrão se nenhuma condição e posição forem válidas.

É aqui que você poderá gerenciar o posicionamento do obturador de acordo com a posição do sol.

- **Azimute** : ângulo de posição do sol.
- **Elevação** : ângulo de altura do sol.
- **Posição** : posição do obturador para tirar se o sol estiver nos limites de azimute e elevação.
- **Condição** : condição adicional a satisfazer para que o obturador tome esta posição (pode estar vazio).
- **COMMENTAIRE** : campos livres para comentários.

>**DICA**
>
>Pequena dica do site [suncalc.org](https://www.suncalc.org) permite, uma vez inserido o seu endereço, ver a posição do sol (e portanto os ângulos azimute e elevação) de acordo com as horas do dia (basta arrastar o pequeno sol no topo).

## Planning

Aqui você pode ver os planos de posicionamento da veneziana feitos no planejamento da Agenda.

## Commandes

- **Azimute do sol** : ângulo atual do azimute do sol.
- **Nascer do sol** : ângulo de elevação atual do sol.
- **Forçar ação** : força a posição do obturador a ser calculada em função da posição do sol e das condições e aplica o resultado a ele, qualquer que seja o estado de gerenciamento (pausado ou não).
- **Última posição** : última posição solicitada do obturador pelo plugin.
- **Status de gerenciamento** : status de gerenciamento (suspenso ou não).
- **Resumo** : força a gestão a voltar ao modo automático (note que este comando deve ser lançado para voltar a gestão automática se tiver modificado a posição da sua veneziana manualmente e marcado a caixa "Não retomar o controlo").
- **Suspender** : suspende o posicionamento automático do obturador.
- **Legal** : atualize os valores dos comandos "Sun azimuth" e "Sun elevation"".
- **Modo** : modo de obturador atual.

Você pode adicionar comandos "mode", o nome do comando será o nome do modo.

# Panel

O plugin possui um painel de gerenciamento para desktop e celular. Para ativá-lo, basta ir até Plugins → Gerenciamento de plug-ins, clicar no plug-in de gerenciamento do painel e, no canto inferior direito, marcar as caixas para exibir os painéis da área de trabalho e móvel.
