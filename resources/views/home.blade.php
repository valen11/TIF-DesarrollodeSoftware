<!doctype html>
<!--
Mock UI: Blade + Alpine.js + Tailwind (single-file template)
3 screens: Dashboard | Catálogos | Mesas & Telegramas
Save as resources/views/mock_electoral.blade.php (Laravel) OR open as standalone HTML (remove Blade @csrf if needed)
--> 
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mock Electoral — Dashboard</title>
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800" x-data="app()">
  <!-- Header -->
  <header class="bg-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <h1 class="text-xl font-semibold">Electoral Mock — 2025</h1>
        <nav class="space-x-2 text-sm text-gray-600">
          <button @click="view='dashboard'" :class="view==='dashboard'? 'text-blue-600 underline':''">Dashboard</button>
          <button @click="view='catalogs'" :class="view==='catalogs'? 'text-blue-600 underline':''">Catálogos</button>
          <button @click="view='mesas'" :class="view==='mesas'? 'text-blue-600 underline':''">Mesas & Telegramas</button>
        </nav>
      </div>
      <div class="text-sm text-gray-600">Usuario: <strong x-text="user"></strong></div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto p-4">
    <!-- Dashboard -->
    <section x-show="view==='dashboard'" x-cloak>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Mesas cargadas</div>
          <div class="text-2xl font-bold" x-text="mesas.length"></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Provincias</div>
          <div class="text-2xl font-bold" x-text="provincias.length"></div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-sm text-gray-500">Listas registradas</div>
          <div class="text-2xl font-bold" x-text="listas.length"></div>
        </div>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Resumen rápido por provincia</h2>
        <div class="grid gap-4 md:grid-cols-2">
          <template x-for="prov in provincias" :key="prov.id">
            <div class="border rounded p-3">
              <div class="flex justify-between items-baseline">
                <div>
                  <div class="font-medium" x-text="prov.nombre"></div>
                  <div class="text-xs text-gray-500">Mesas: <span x-text="countMesasProv(prov.nombre)"></span></div>
                </div>
                <div class="text-right">
                  <div class="text-sm text-gray-500">Participación estimada</div>
                  <div class="font-bold" x-text="formatPercent(participacionProv(prov.nombre))"></div>
                </div>
              </div>
              <div class="mt-3">
                <table class="w-full text-sm">
                  <thead class="text-xs text-gray-500">
                    <tr><th class="text-left">Lista</th><th class="text-right">Votos</th><th class="text-right">% válidos</th></tr>
                  </thead>
                  <tbody>
                    <template x-for="row in resultadosProvinciaTabla(prov.nombre,'DIPUTADOS')" :key="row.lista">
                      <tr>
                        <td x-text="row.lista"></td>
                        <td class="text-right" x-text="row.votos"></td>
                        <td class="text-right" x-text="formatPercent(row.pct)"></td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </div>
          </template>
        </div>
      </div>
    </section>

    <!-- Catalogs -->
    <section x-show="view==='catalogs'" x-cloak>
      <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Provincias</h2>
          <form @submit.prevent="addProvincia()" class="flex gap-2">
            <input x-model="nuevoProv" class="flex-1 border rounded px-2 py-1" placeholder="Nombre provincia" />
            <button class="bg-blue-600 text-white px-3 rounded">Agregar</button>
          </form>
          <ul class="mt-3 text-sm">
            <template x-for="p in provincias" :key="p.id">
              <li class="flex justify-between items-center py-1">
                <div x-text="p.nombre"></div>
                <div class="text-xs text-gray-500">ID: <span x-text="p.id"></span></div>
              </li>
            </template>
          </ul>
        </div>

        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Listas / Candidatos</h2>
          <form @submit.prevent="addLista()" class="grid grid-cols-1 gap-2">
            <div class="flex gap-2">
              <select x-model="listaForm.provincia" class="border rounded px-2 py-1 flex-1">
                <option value="">Provincia</option>
                <template x-for="p in provincias" :key="p.id"><option x-bind:value="p.nombre" x-text="p.nombre"></option></template>
              </select>
              <select x-model="listaForm.cargo" class="border rounded px-2 py-1 w-40">
                <option value="DIPUTADOS">DIPUTADOS</option>
                <option value="SENADORES">SENADORES</option>
              </select>
            </div>
            <input x-model="listaForm.lista" placeholder="Nombre lista" class="border rounded px-2 py-1" />
            <input x-model="listaForm.alianza" placeholder="Alianza (opcional)" class="border rounded px-2 py-1" />
            <div class="flex gap-2">
              <button class="bg-green-600 text-white px-3 rounded">Agregar lista</button>
              <button type="button" @click="openImportDialog('listas')" class="bg-gray-200 px-3 rounded">Importar CSV</button>
            </div>
          </form>

          <div class="mt-3 text-sm">
            <table class="w-full">
              <thead class="text-xs text-gray-500"><tr><th>Provincia</th><th>Lista</th><th>Alianza</th></tr></thead>
              <tbody>
                <template x-for="l in listas" :key="l.id">
                  <tr><td x-text="l.provincia"></td><td x-text="l.lista"></td><td x-text="l.alianza"></td></tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Mesas & Telegramas -->
    <section x-show="view==='mesas'" x-cloak>
      <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Mesas (catálogo)</h2>
          <form @submit.prevent="addMesa()" class="grid gap-2">
            <input x-model="mesaForm.id_mesa" placeholder="ID mesa" class="border rounded px-2 py-1" />
            <input x-model="mesaForm.provincia" placeholder="Provincia" class="border rounded px-2 py-1" />
            <input x-model="mesaForm.circuito" placeholder="Circuito" class="border rounded px-2 py-1" />
            <input x-model.number="mesaForm.electores" placeholder="Electores" type="number" class="border rounded px-2 py-1" />
            <div class="flex gap-2">
              <button class="bg-green-600 text-white px-3 rounded">Agregar mesa</button>
              <button type="button" @click="openImportDialog('mesas')" class="bg-gray-200 px-3 rounded">Importar CSV</button>
            </div>
          </form>

          <div class="mt-3 text-sm max-h-64 overflow-auto">
            <table class="w-full text-sm">
              <thead class="text-xs text-gray-500"><tr><th>Mesa</th><th>Prov</th><th>Electores</th></tr></thead>
              <tbody>
                <template x-for="m in mesas" :key="m.id_mesa"><tr><td x-text="m.id_mesa"></td><td x-text="m.provincia"></td><td x-text="m.electores"></td></tr></template>
              </tbody>
            </table>
          </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Ingresar Telegrama</h2>
          <form @submit.prevent="addTelegrama()" class="grid gap-2 text-sm">
            <select x-model="tgForm.id_mesa" class="border rounded px-2 py-1">
              <option value="">Seleccionar mesa</option>
              <template x-for="m in mesas" :key="m.id_mesa"><option x-bind:value="m.id_mesa">{{m.id_mesa}}</option></template>
            </select>

            <div class="text-xs text-gray-600">Votos por lista (campo obligatorio). Los blancos, nulos y recurridos se ingresan abajo.</div>
            <template x-for="l in listas.filter(x=> x.cargo==='DIPUTADOS')" :key="l.id">
              <div class="flex gap-2 items-center">
                <div class="w-36 text-sm" x-text="l.lista + ' ('+l.provincia+')'"></div>
                <input type="number" min="0" x-model.number="tgForm.votos[l.lista]" class="border rounded px-2 py-1 flex-1" />
              </div>
            </template>

            <div class="flex gap-2">
              <input x-model.number="tgForm.blancos" type="number" class="border rounded px-2 py-1" placeholder="blancos" />
              <input x-model.number="tgForm.nulos" type="number" class="border rounded px-2 py-1" placeholder="nulos" />
              <input x-model.number="tgForm.recurridos" type="number" class="border rounded px-2 py-1" placeholder="recurridos" />
            </div>

            <div class="flex gap-2">
              <button class="bg-blue-600 text-white px-3 rounded">Guardar telegrama</button>
              <button type="button" @click="openImportDialog('telegramas')" class="bg-gray-200 px-3 rounded">Importar CSV</button>
            </div>

            <template x-if="tgError"><div class="mt-2 text-red-600 text-sm" x-text="tgError"></div></template>
            <template x-if="tgSuccess"><div class="mt-2 text-green-600 text-sm" x-text="tgSuccess"></div></template>
          </form>

          <div class="mt-4 text-sm">
            <div class="flex justify-between items-center">
              <div class="font-medium">Telegramas recientes</div>
              <div class="text-xs text-gray-500">(últimas versiones)</div>
            </div>
            <div class="max-h-40 overflow-auto mt-2">
              <template x-for="t in telegramas.slice().reverse()" :key="t.id">
                <div class="border-b py-2">
                  <div class="flex justify-between">
                    <div class="text-sm">Mesa <strong x-text="t.id_mesa"></strong> — <span x-text="t.provincia"></span></div>
                    <div class="text-xs text-gray-500">v <span x-text="t.version"></span> — <span x-text="t.fecha_hora"></span></div>
                  </div>
                  <div class="text-xs text-gray-600">Blancos: <span x-text="t.blancos"></span> | Nulos: <span x-text="t.nulos"></span> | Rec: <span x-text="t.recurridos"></span></div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-2">Reportes / Export</h3>
        <div class="flex gap-2 items-center">
          <select x-model="exportProv" class="border rounded px-2 py-1">
            <option value="">Seleccionar provincia (todo = nacional)</option>
            <template x-for="p in provincias" :key="p.id"><option x-bind:value="p.nombre" x-text="p.nombre"></option></template>
          </select>
          <select x-model="exportCargo" class="border rounded px-2 py-1">
            <option value="DIPUTADOS">DIPUTADOS</option>
            <option value="SENADORES">SENADORES</option>
          </select>
          <button @click="exportCSV()" class="bg-indigo-600 text-white px-3 rounded">Exportar CSV</button>
          <button @click="showDhondtModal()" class="bg-yellow-500 text-black px-3 rounded">Ver D'Hondt (provincia)</button>
        </div>
      </div>
    </section>

    <!-- D'Hondt Modal -->
    <div x-show="dhondtModal" x-cloak class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
      <div class="bg-white w-11/12 md:w-2/3 p-4 rounded shadow">
        <div class="flex justify-between items-center mb-2">
          <h3 class="font-semibold">Simular D'Hondt — <span x-text="dhProv"></span></h3>
          <button @click="dhondtModal=false" class="text-gray-500">Cerrar</button>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="text-xs text-gray-600">Número de bancas</label>
            <input type="number" x-model.number="dhBancas" min="1" class="border rounded px-2 py-1 w-24" />
            <label class="ml-3 text-xs text-gray-600">Umbral %</label>
            <input type="number" x-model.number="dhUmbral" min="0" step="0.1" class="border rounded px-2 py-1 w-20" />
            <div class="mt-3 text-sm">
              <table class="w-full text-sm">
                <thead class="text-xs text-gray-500"><tr><th>Lista</th><th class="text-right">Votos</th></tr></thead>
                <tbody>
                  <template x-for="r in resultadosProvinciaTabla(dhProv,'DIPUTADOS')" :key="r.lista">
                    <tr><td x-text="r.lista"></td><td class="text-right" x-text="r.votos"></td></tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
          <div>
            <div class="font-medium mb-2">Asignación</div>
            <div class="text-sm">
              <template x-for="(count,lista) in dhondtCompute(dhProv,dhBancas,dhUmbral)" :key="lista">
                <div class="flex justify-between py-1 border-b"><div x-text="lista"></div><div x-text="count"></div></div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Import hidden input -->
    <input type="file" id="importFile" class="hidden" @change="handleFileUpload($event)" />

  </main>

  <script>
    function app(){
      return {
        user: 'Valentina',
        view: 'dashboard',
        // sample seed data
        provincias: [ {id:'BA', nombre:'Buenos Aires'}, {id:'CB', nombre:'Córdoba'} ],
        listas: [
          {id:'L_A', provincia:'Buenos Aires', cargo:'DIPUTADOS', lista:'Lista A', alianza:'Frente X'},
          {id:'L_B', provincia:'Buenos Aires', cargo:'DIPUTADOS', lista:'Lista B', alianza:'Frente Y'},
          {id:'L_C', provincia:'Córdoba', cargo:'DIPUTADOS', lista:'Lista C', alianza:'Frente Z'},
          {id:'L_SA', provincia:'Buenos Aires', cargo:'SENADORES', lista:'Lista A', alianza:'Frente X'}
        ],
        mesas: [
          {id_mesa:1001, provincia:'Buenos Aires', circuito:'0101', establecimiento:'Escuela 12', electores:350},
          {id_mesa:1002, provincia:'Buenos Aires', circuito:'0102', establecimiento:'Escuela 34', electores:280},
          {id_mesa:1003, provincia:'Buenos Aires', circuito:'0103', establecimiento:'Escuela 7', electores:300},
          {id_mesa:1004, provincia:'Buenos Aires', circuito:'0104', establecimiento:'Escuela 18', electores:260},
          {id_mesa:2001, provincia:'Córdoba', circuito:'0201', establecimiento:'Escuela 2', electores:220},
          {id_mesa:2002, provincia:'Córdoba', circuito:'0202', establecimiento:'Escuela 5', electores:210},
          {id_mesa:2003, provincia:'Córdoba', circuito:'0203', establecimiento:'Escuela 6', electores:190},
          {id_mesa:2004, provincia:'Córdoba', circuito:'0204', establecimiento:'Escuela 9', electores:200}
        ],
        // telegramas stored as last-version per mesa
        telegramas: [
          {id:'t_1001_v1', id_mesa:1001, provincia:'Buenos Aires', version:1, fecha_hora:'2025-09-29T16:00:00', listas:[{lista:'Lista A', votos:120},{lista:'Lista B', votos:100},{lista:'Lista C', votos:60}], blancos:8, nulos:5, recurridos:1},
          {id:'t_1002_v1', id_mesa:1002, provincia:'Buenos Aires', version:1, fecha_hora:'2025-09-29T16:10:00', listas:[{lista:'Lista A', votos:140},{lista:'Lista B', votos:90}], blancos:6, nulos:3, recurridos:0},
          {id:'t_2001_v1', id_mesa:2001, provincia:'Córdoba', version:1, fecha_hora:'2025-09-29T16:20:00', listas:[{lista:'Lista C', votos:150}], blancos:4, nulos:2, recurridos:0}
        ],

        // forms
        nuevoProv: '',
        listaForm: {provincia:'', cargo:'DIPUTADOS', lista:'', alianza:''},
        mesaForm: {id_mesa:'', provincia:'', circuito:'', electores:0},
        tgForm: {id_mesa:'', votos: {}, blancos:0, nulos:0, recurridos:0},
        tgError: '', tgSuccess: '',

        // export / dhondt state
        exportProv: '', exportCargo:'DIPUTADOS',
        dhondtModal:false, dhProv:'Buenos Aires', dhBancas:5, dhUmbral:1.5,

        // import helper state
        importTarget: null,

        // helpers
        addProvincia(){
          if(!this.nuevoProv.trim()) return;
          const id = this.nuevoProv.trim().split(' ').map(s=>s[0]).join('').toUpperCase() + Date.now().toString().slice(-3);
          this.provincias.push({id, nombre:this.nuevoProv.trim()});
          this.nuevoProv='';
        },
        addLista(){
          if(!this.listaForm.lista || !this.listaForm.provincia) return;
          const id='L_'+Math.random().toString(36).slice(2,8);
          this.listas.push({id, provincia:this.listaForm.provincia, cargo:this.listaForm.cargo, lista:this.listaForm.lista, alianza:this.listaForm.alianza});
          this.listaForm = {provincia:'', cargo:'DIPUTADOS', lista:'', alianza:''};
        },
        addMesa(){
          if(!this.mesaForm.id_mesa) return;
          // simple check unique
          if(this.mesas.find(m=>m.id_mesa==this.mesaForm.id_mesa)) return alert('Mesa ya existe');
          this.mesas.push({...this.mesaForm});
          this.mesaForm = {id_mesa:'', provincia:'', circuito:'', electores:0};
        },
        addTelegrama(){
          this.tgError=''; this.tgSuccess='';
          if(!this.tgForm.id_mesa){ this.tgError='Seleccione una mesa.'; return; }
          const mesa = this.mesas.find(m=>m.id_mesa==this.tgForm.id_mesa);
          if(!mesa){ this.tgError='Mesa no existe en catálogo.'; return; }
          // compute sum votos (only DIPUTADOS lists considered here for demo)
          const votosSum = Object.values(this.tgForm.votos).reduce((s,n)=>s+(Number(n)||0),0);
          const tot = votosSum + (Number(this.tgForm.blancos)||0) + (Number(this.tgForm.nulos)||0) + (Number(this.tgForm.recurridos)||0);
          if(tot > mesa.electores){ this.tgError = `Total votos (${tot}) > electores (${mesa.electores}) en mesa ${mesa.id_mesa}`; return; }
          // build telegrama record
          const listasArr = Object.keys(this.tgForm.votos).map(k=>({lista:k, votos: Number(this.tgForm.votos[k]||0)}));
          const id = 't_'+this.tgForm.id_mesa+'_v'+( (this.telegramas.filter(t=>t.id_mesa==this.tgForm.id_mesa).length)+1 );
          const rec = {id, id_mesa:this.tgForm.id_mesa, provincia:mesa.provincia, version:1, fecha_hora: new Date().toISOString(), listas: listasArr, blancos: Number(this.tgForm.blancos||0), nulos: Number(this.tgForm.nulos||0), recurridos: Number(this.tgForm.recurridos||0)};
          // if exists replace last (simple logic: allow multiple entries per mesa but keep latest)
          const existingIndex = this.telegramas.findIndex(t=>t.id_mesa==this.tgForm.id_mesa);
          if(existingIndex>=0){ rec.version = this.telegramas[existingIndex].version + 1; this.telegramas[existingIndex]=rec; }
          else this.telegramas.push(rec);
          this.tgSuccess='Telegrama guardado.';
          // reset votes
          this.tgForm = {id_mesa:'', votos:{}, blancos:0, nulos:0, recurridos:0};
        },

        countMesasProv(prov){ return this.mesas.filter(m=>m.provincia===prov).length; },

        participacionProv(prov){
          const mesas = this.mesas.filter(m=>m.provincia===prov);
          if(!mesas.length) return 0;
          let electores = 0; let emitidos = 0;
          for(const m of mesas){ electores += Number(m.electores||0); const tg = this.telegramas.find(t=>t.id_mesa==m.id_mesa); if(tg){ const sumlistas = tg.listas.reduce((s,x)=>s+(Number(x.votos)||0),0); emitidos += sumlistas + (tg.blancos||0) + (tg.nulos||0) + (tg.recurridos||0); } }
          if(electores===0) return 0; return electores? (emitidos / electores) : 0;
        },

        resultadosProvinciaTabla(prov, cargo){
          // sum votos por lista for province & cargo
          const lists = this.listas.filter(l=> l.provincia===prov && l.cargo===cargo);
          const map = {};
          for(const l of lists) map[l.lista] = 0;
          // walk telegramas for mesas in province
          const mesasProv = this.mesas.filter(m=>m.provincia===prov).map(m=>m.id_mesa);
          for(const t of this.telegramas.filter(t=> mesasProv.includes(t.id_mesa))){
            for(const li of t.listas){ if(map[li.lista]!==undefined) map[li.lista] += Number(li.votos||0); else map[li.lista] = Number(li.votos||0); }
          }
          // compute votos validos
          const votosValidos = Object.values(map).reduce((s,n)=>s+Number(n||0),0);
          const rows = Object.keys(map).map(k=>({lista:k, votos:map[k], pct: votosValidos? map[k]/votosValidos:0 }));
          rows.sort((a,b)=>b.votos - a.votos);
          return rows;
        },

        formatPercent(v){ return (Number(v||0)*100).toFixed(2)+'%'; },

        openImportDialog(target){ this.importTarget = target; document.getElementById('importFile').click(); },
        handleFileUpload(e){ const file = e.target.files[0]; if(!file) return; const reader = new FileReader(); reader.onload = (ev)=>{ const text = ev.target.result; this.parseCSVImport(text, this.importTarget); }; reader.readAsText(file); e.target.value=''; },
        parseCSVImport(text, target){ // very simple CSV parser for demo (expects headers)
          const lines = text.split(/\r?\n/).filter(l=>l.trim()); if(!lines.length) return alert('CSV vacío');
          const headers = lines[0].split(',').map(h=>h.trim());
          const rows = lines.slice(1).map(l=>{ const parts = l.split(','); const obj={}; headers.forEach((h,i)=> obj[h]=parts[i]===undefined?'':parts[i].trim()); return obj; });
          const errors=[];
          if(target==='mesas'){
            for(const r of rows){ if(!r.id_mesa) { errors.push('fila sin id_mesa'); continue;} this.mesas.push({id_mesa: r.id_mesa, provincia: r.provincia, circuito: r.circuito, establecimiento: r.establecimiento, electores: Number(r.electores||0)}); }
          } else if(target==='listas'){
            for(const r of rows){ const id='L_'+Math.random().toString(36).slice(2,8); this.listas.push({id, provincia: r.provincia, cargo: r.cargo||'DIPUTADOS', lista: r.lista, alianza: r.alianza||''}); }
          } else if(target==='telegramas'){
            // expect: id_mesa,provincia,lista,votos_diputados,blancos,nulos,recurridos
            // group by id_mesa
            const grouped = {};
            for(const r of rows){ const idm = r.id_mesa; if(!grouped[idm]) grouped[idm]= {id_mesa: idm, provincia: r.provincia, listas: [], blancos: Number(r.blancos||0), nulos: Number(r.nulos||0), recurridos: Number(r.recurridos||0)}; grouped[idm].listas.push({lista: r.lista, votos: Number(r.votos_diputados||0)}); }
            for(const k in grouped){ const g = grouped[k]; const mesa = this.mesas.find(m=> m.id_mesa==g.id_mesa);
              if(!mesa){ errors.push(`Mesa ${g.id_mesa} no encontrada`); continue; }
              const totalListas = g.listas.reduce((s,x)=>s+x.votos,0); const tot = totalListas + g.blancos + g.nulos + g.recurridos; if(tot > mesa.electores) errors.push(`Total votos (${tot}) > electores (${mesa.electores}) en mesa ${g.id_mesa}`);
              const id='t_'+g.id_mesa+'_v1'; this.telegramas.push({id, id_mesa: g.id_mesa, provincia: g.provincia, version:1, fecha_hora: new Date().toISOString(), listas: g.listas, blancos: g.blancos, nulos: g.nulos, recurridos: g.recurridos});
            }
          }
          if(errors.length) alert('Import terminado con errores:\n' + errors.join('\n')); else alert('Import OK');
        },

        // export CSV: build aggregated by list (provincial or national)
        exportCSV(){
          const prov = this.exportProv; const cargo = this.exportCargo;
          let resultsMap = {};
          const mesasUsed = prov? this.mesas.filter(m=>m.provincia===prov).map(m=>m.id_mesa): this.mesas.map(m=>m.id_mesa);
          for(const tg of this.telegramas.filter(t=> mesasUsed.includes(t.id_mesa))){
            for(const l of tg.listas){ if(!resultsMap[l.lista]) resultsMap[l.lista] = 0; resultsMap[l.lista] += Number(l.votos||0); }
          }
          // compose CSV
          const rows = [['lista','votos']]; for(const k of Object.keys(resultsMap)) rows.push([k, resultsMap[k]]);
          const csv = rows.map(r=> r.join(',')).join('\n');
          const blob = new Blob([csv], {type:'text/csv'});
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a'); a.href=url; a.download = (prov?prov:'Nacional') + '_' + cargo + '_resultados.csv'; a.click(); URL.revokeObjectURL(url);
        },

        // simple D'Hondt implementation in JS
        dhondtCompute(prov, bancas, umbralPct){
          const rows = this.resultadosProvinciaTabla(prov,'DIPUTADOS');
          const votosValidos = rows.reduce((s,r)=>s+r.votos,0);
          const filtered = rows.filter(r=> (votosValidos? (r.votos / votosValidos * 100) : 0) >= umbralPct);
          // build divisors
          const pool = [];
          for(const r of filtered){ for(let d=1; d<=bancas; d++){ pool.push({lista:r.lista, valor: r.votos/d}); } }
          pool.sort((a,b)=> b.valor - a.valor);
          const top = pool.slice(0, bancas);
          const assigned = {};
          for(const t of top){ assigned[t.lista] = (assigned[t.lista]||0) + 1; }
          // ensure all filtered lists appear in output with 0 if none assigned
          for(const r of filtered) if(assigned[r.lista]===undefined) assigned[r.lista]=0;
          return assigned;
        },

        showDhondtModal(){ this.dhProv = this.exportProv || (this.provincias[0] && this.provincias[0].nombre) || 'Buenos Aires'; this.dhondtModal=true; }
      }
    }
  </script>
</body>
</html>