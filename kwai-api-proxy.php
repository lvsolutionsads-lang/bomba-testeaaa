<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kwai Ads Manager</title>
    <meta name="description" content="Kwai Ads Manager" />
    <link rel="icon" href="/favicon.ico" />
    <script type="module" crossorigin src="/assets/index-BQk-x3rp.js"></script>
    <link rel="stylesheet" crossorigin href="/assets/index-Hlt1zEdO.css">
  </head>
  <body>
    <div id="root"></div>
    <script>
    function injectAgentField() {
      if (document.getElementById('kwai-agent-id-field')) return;
      var cards = document.querySelectorAll('h2, h3, h1');
      var tokenCard = null;
      for (var i = 0; i < cards.length; i++) {
        if (cards[i].textContent && cards[i].textContent.indexOf('Access Token') !== -1) {
          tokenCard = cards[i].closest('div[class*="rounded"]') || cards[i].parentElement.parentElement;
          break;
        }
      }
      if (!tokenCard) return;

      var savedAgentId = localStorage.getItem('kwai_agent_id') || '';
      var savedCorpId = localStorage.getItem('kwai_corp_id') || '';

      var wrapper = document.createElement('div');
      wrapper.id = 'kwai-agent-id-field';
      wrapper.style.cssText = 'margin-top:16px;padding:16px;background:hsl(30,5%,96%);border-radius:12px;border:1px solid hsl(30,5%,88%);';
      wrapper.innerHTML = '<div style="margin-bottom:10px;"><span style="font-size:15px;font-weight:700;color:#222;">&#127970; Agent ID / Corp ID</span><p style="font-size:12px;color:#888;margin:4px 0 0;">Obrigatório para buscar contas. Preencha UM dos dois campos.</p></div>'
        + '<div style="display:flex;gap:10px;margin-bottom:10px;">'
        + '<div style="flex:1;"><label style="font-size:12px;color:#666;">Agent ID (agência)</label><input id="kwai-agent-input" type="text" value="' + savedAgentId + '" placeholder="Ex: 123456" style="width:100%;padding:10px;border-radius:8px;border:1px solid #ddd;background:#fff;color:#222;font-family:monospace;font-size:14px;margin-top:4px;box-sizing:border-box;" /></div>'
        + '<div style="flex:1;"><label style="font-size:12px;color:#666;">Corp ID (anunciante)</label><input id="kwai-corp-input" type="text" value="' + savedCorpId + '" placeholder="Ex: 789012" style="width:100%;padding:10px;border-radius:8px;border:1px solid #ddd;background:#fff;color:#222;font-family:monospace;font-size:14px;margin-top:4px;box-sizing:border-box;" /></div>'
        + '</div>'
        + '<button id="kwai-save-ids-btn" style="background:#f97316;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;width:100%;">&#128190; Salvar IDs</button>'
        + '<p id="kwai-ids-status" style="font-size:12px;color:#16a34a;margin-top:6px;display:none;"></p>';

      tokenCard.parentNode.insertBefore(wrapper, tokenCard.nextSibling);

      document.getElementById('kwai-save-ids-btn').addEventListener('click', function() {
        var agentId = document.getElementById('kwai-agent-input').value.trim();
        var corpId = document.getElementById('kwai-corp-input').value.trim();
        if (agentId) localStorage.setItem('kwai_agent_id', agentId);
        else localStorage.removeItem('kwai_agent_id');
        if (corpId) localStorage.setItem('kwai_corp_id', corpId);
        else localStorage.removeItem('kwai_corp_id');
        var st = document.getElementById('kwai-ids-status');
        st.style.display = 'block';
        st.textContent = '\u2705 IDs salvos! Agora clique em "Salvar Token e Buscar Contas"';
        setTimeout(function(){ st.style.display = 'none'; }, 5000);
      });
    }

    var obs = new MutationObserver(function() {
      if (window.location.pathname === '/settings') {
        setTimeout(injectAgentField, 500);
        setTimeout(injectAgentField, 1500);
      }
    });
    obs.observe(document.body, { childList: true, subtree: true });
    window.addEventListener('load', function() {
      setTimeout(injectAgentField, 1000);
      setTimeout(injectAgentField, 3000);
    });
    </script>
  </body>
</html>
