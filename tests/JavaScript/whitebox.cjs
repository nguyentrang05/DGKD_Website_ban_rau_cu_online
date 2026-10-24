/* No npm dependency. Execute JavaScript extracted from the real Blade files. */
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const assert = require('node:assert/strict');

function option(name, fallback) {
  const index = process.argv.indexOf(name);
  return index < 0 ? fallback : process.argv[index + 1];
}
const projectRoot = path.resolve(option('--project-root', path.join(__dirname, '../..')));
const suite = option('--suite', 'core');
if (!['core', 'requirements', 'all'].includes(suite)) throw new Error('Use --suite core, requirements, or all');

const products = [
  { id: 1, name: 'Cà rốt', description: 'Củ tươi', price: 15000, unit: 'kg', stock_quantity: 10, category: {name: 'Củ', slug: 'cu'}, image_url: '/storage/carrot.jpg', views: 10, created_at: '2026-01-01' },
  { id: 2, name: 'Cải xanh', description: 'Rau xanh', price: 50000, unit: 'bó', stock_quantity: 10, category: {name: 'Rau', slug: 'rau'}, image_url: null, views: 30, created_at: '2026-01-06' },
  { id: 3, name: 'Táo đỏ', description: 'Quả tươi', price: 100000, unit: 'kg', stock_quantity: 10, category: {name: 'Quả', slug: 'qua'}, image_url: null, views: 20, created_at: '2026-01-04' },
  { id: 4, name: 'Dưa lưới', description: 'Quả ngọt', price: 200000, unit: 'kg', stock_quantity: 10, category: {name: 'Quả', slug: 'qua'}, image_url: null, views: 5, created_at: '2026-01-03' },
  { id: 5, name: 'Nấm premium', description: 'Nấm tươi', price: 200001, unit: 'gói', stock_quantity: 0, category: {name: 'Nấm', slug: 'nam'}, image_url: null, views: 15, created_at: '2026-01-02' },
  { id: 6, name: 'Chuối @Hữu cơ', description: 'Quả chín', price: 49999, unit: 'kg', stock_quantity: 10, category: {name: 'Quả', slug: 'qua'}, image_url: null, views: 40, created_at: '2026-01-05' },
  { id: 7, name: 'Khoai tây', description: 'Củ sạch', price: 99999, unit: 'kg', stock_quantity: 10, category: {name: 'Củ', slug: 'cu'}, image_url: null, views: 25, created_at: '2026-01-07' },
  { id: 8, name: 'Bắp cải', description: 'Rau sạch', price: 100001, unit: 'kg', stock_quantity: 10, category: {name: 'Rau', slug: 'rau'}, image_url: null, views: 35, created_at: '2026-01-08' },
];

function plain(value) { return JSON.parse(JSON.stringify(value)); }
function element() {
  return {
    value: '', type: 'password', innerHTML: '', textContent: '', style: {}, events: {},
    classList: { add() {}, remove() {} },
    addEventListener(event, callback) { this.events[event] = callback; },
    appendChild() {}, remove() {}, scrollIntoView() {},
    getAttribute() { return 'test-csrf'; },
    querySelector() { return this.icon || (this.icon = element()); },
  };
}

function environment(relativeFile, data = products) {
  const absolute = path.join(projectRoot, relativeFile);
  const blade = fs.readFileSync(absolute, 'utf8');
  const scripts = [...blade.matchAll(/<script\b[^>]*>([\s\S]*?)<\/script>/gi)];
  assert.ok(scripts.length > 0, `No inline JavaScript found in ${relativeFile}`);
  let source = scripts.map(match => match[1]).join('\n');
  if (relativeFile.includes('products/index')) {
    const declaration = 'const allProducts = @json($allProducts);';
    assert.ok(source.includes(declaration), 'Blade data declaration changed: review test adapter.');
    source = source.replace(declaration, 'const allProducts = '+JSON.stringify(data)+';');
  }
  const elements = new Map();
  const timers = new Map();
  let nextTimer = 0;
  const document = {
    head: element(), body: element(),
    getElementById(id) {
      if (!elements.has(id)) elements.set(id, element());
      return elements.get(id);
    },
    addEventListener() {}, // Deliberately do not auto-fire DOMContentLoaded.
    createElement() { return element(); },
    querySelector(selector) { return this.getElementById(selector); },
    querySelectorAll() { return []; },
  };
  const context = vm.createContext({
    document, console, Intl, URL,
    setTimeout(callback) { timers.set(++nextTimer, callback); return nextTimer; },
    clearTimeout(id) { timers.delete(id); },
    window: { location: { reload() {} } },
    confirm() { return false; },
  });
  vm.runInContext(source, context, { filename: relativeFile, timeout: 1000 });
  function evaluate(code) { return vm.runInContext(code, context, { timeout: 1000 }); }
  return {
    context, document, evaluate,
    filters(overrides = {}) {
      evaluate('currentFilters = '+JSON.stringify({ category: '', priceRange: '', search: '', sort: 'latest', ...overrides }));
    },
    ids() { return plain(evaluate('filterProducts(allProducts).map(p => p.id)')); },
    flush() {
      let count = 0;
      while (timers.size) {
        assert.ok(++count < 100, 'Unexpected timer loop');
        const [id, callback] = timers.entries().next().value;
        timers.delete(id); callback();
      }
    },
  };
}

const catalogue = () => environment('resources/views/products/index.blade.php');
const cases = [];
function add(name, run, group = 'core') { cases.push({name, run, group}); }

add('JS01_no_filters_preserve_all_products', () => {
  const app = catalogue(); assert.deepEqual(app.ids(), [1,2,3,4,5,6,7,8]);
});
for (const [id, search, expected] of [
  ['JS02', 'Cà rốt', [1]], ['JS03', 'rốt', [1]], ['JS04', 'RAU XANH', [2]],
  ['JS05', 'CÀ RỐT', [1]], ['JS06', 'khong-ton-tai', []], ['JS07', '@', [6]],
]) {
  add(id+'_search_'+search, () => {
    const app = catalogue(); app.filters({search}); assert.deepEqual(app.ids(), expected);
  });
}
for (const [id, input, expected] of [['JS08','   ',[8,7,2,6,3,4,5,1]], ['JS09','  rốt  ',[1]]]) {
  add(id+'_search_handler_trims_input_and_resets_page', () => {
    const app = catalogue(); app.document.getElementById('searchInput').value = input;
    app.evaluate('currentPage = 2; handleSearchChange()'); app.flush();
    assert.equal(app.evaluate('currentPage'), 1);
    assert.deepEqual(plain(app.evaluate('filteredProducts.map(p => p.id)')), expected);
  });
}
add('JS10_category_filter', () => {
  const app = catalogue(); app.filters({category:'cu'}); assert.deepEqual(app.ids(), [1,7]);
});
for (const [id, priceRange, expected] of [
  ['JS11','0-50000',[1,6]], ['JS12','50000-100000',[2,3,7]],
  ['JS13','100000-200000',[3,4,8]], ['JS14','200000+',[5]],
]) {
  add(id+'_price_boundary_'+priceRange, () => {
    const app = catalogue(); app.filters({priceRange}); assert.deepEqual(app.ids(), expected);
  });
}
add('JS15_unknown_price_token_uses_existing_default_branch', () => {
  const app = catalogue(); app.filters({priceRange:'unknown-token'});
  assert.deepEqual(app.ids(), [1,2,3,4,5,6,7,8]);
});
add('JS16_combined_category_price_and_search', () => {
  const app = catalogue(); app.filters({category:'qua',priceRange:'100000-200000',search:'táo'});
  assert.deepEqual(app.ids(), [3]);
});
add('JS17_combined_filters_no_matches', () => {
  const app = catalogue(); app.filters({category:'rau',priceRange:'200000+'});
  assert.deepEqual(app.ids(), []);
});
for (const [id, sort, expected] of [
  ['JS18','price_asc',[1,6,2,7,3,8,4,5]],
  ['JS19','price_desc',[5,4,8,3,7,2,6,1]],
  ['JS20','popular',[6,8,2,7,3,5,1,4]],
  ['JS21','latest',[8,7,2,6,3,4,5,1]],
  ['JS22','unknown-sort',[8,7,2,6,3,4,5,1]],
]) {
  add(id+'_sort_'+sort, () => {
    const app = catalogue(); app.filters({sort});
    assert.deepEqual(plain(app.evaluate('sortProducts(allProducts).map(p => p.id)')), expected);
  });
}
add('JS23_name_sort_orders_by_locale_compare', () => {
  const app = catalogue(); app.filters({sort:'name_asc'});
  const result = plain(app.evaluate('sortProducts(allProducts).map(p => p.name)'));
  for (let i=1;i<result.length;i++) assert.ok(result[i-1].localeCompare(result[i]) <= 0);
  assert.equal(result.length, products.length);
});
add('JS24_product_card_contains_name_price_unit_image_and_detail_link', () => {
  const app = catalogue(); const html = app.evaluate('renderProductCard(allProducts[0])');
  for (const text of ['Cà rốt','15.000','/kg','/storage/carrot.jpg','/products/1','Còn hàng (10)']) {
    assert.ok(html.includes(text), text);
  }
});
add('JS25_out_of_stock_card_is_disabled_with_fallback_image', () => {
  const app = catalogue(); const html = app.evaluate('renderProductCard(allProducts[4])');
  assert.ok(html.includes('Hết hàng')); assert.ok(html.includes('disabled pointer-events-none'));
  assert.ok(html.includes('images/products/default.jpg'));
});
add('JS26_empty_results_with_filters_have_clear_button', () => {
  const app = catalogue(); app.filters({search:'missing'});
  app.evaluate('filteredProducts = []; renderProducts()');
  const html = app.document.getElementById('productsGrid').innerHTML;
  assert.ok(html.includes('Không tìm thấy sản phẩm')); assert.ok(html.includes('Xóa bộ lọc'));
});
add('JS27_empty_catalogue_without_filters_has_distinct_message', () => {
  const app = catalogue(); app.evaluate('filteredProducts = []; renderProducts()');
  const html = app.document.getElementById('productsGrid').innerHTML;
  assert.ok(html.includes('Hiện tại chưa có sản phẩm')); assert.ok(!html.includes('Xóa bộ lọc'));
});
add('JS28_first_page_has_four_cards', () => {
  const app = catalogue(); app.evaluate('renderProducts()');
  const html = app.document.getElementById('productsGrid').innerHTML;
  assert.equal((html.match(/class="card h-100/g)||[]).length, 4);
  assert.ok(html.includes('/products/1')); assert.ok(!html.includes('/products/5'));
});
add('JS29_second_page_has_remaining_cards', () => {
  const app = catalogue(); app.evaluate('currentPage=2; renderProducts()');
  const html = app.document.getElementById('productsGrid').innerHTML;
  assert.equal((html.match(/class="card h-100/g)||[]).length, 4);
  assert.ok(html.includes('/products/5')); assert.ok(!html.includes('/products/1'));
});
add('JS30_pagination_hidden_for_one_page', () => {
  const app = catalogue(); app.evaluate('filteredProducts=allProducts.slice(0,4); renderPagination()');
  assert.equal(app.document.getElementById('paginationContainer').innerHTML, '');
});
add('JS31_pagination_multiple_pages_has_prev_next_and_ellipsis', () => {
  const app = catalogue();
  app.evaluate('filteredProducts=Array.from({length:40},(_,i)=>({...allProducts[0],id:i+1})); currentPage=5; renderPagination()');
  const html = app.document.getElementById('paginationContainer').innerHTML;
  for (const text of ['Trước','Sau','...','goToPage(10)','goToPage(5)']) assert.ok(html.includes(text));
});
add('JS32_page_navigation_accepts_valid_page_and_rejects_zero_or_too_large', () => {
  const app = catalogue(); app.evaluate('goToPage(2)'); assert.equal(app.evaluate('currentPage'),2);
  app.evaluate('goToPage(0); goToPage(3)'); assert.equal(app.evaluate('currentPage'),2);
});
add('JS33_clear_filters_resets_controls_state_and_page', () => {
  const app = catalogue(); app.filters({search:'rốt',category:'cu',priceRange:'0-50000',sort:'price_desc'});
  app.evaluate('currentPage=2; clearAllFilters()'); app.flush();
  assert.equal(app.document.getElementById('searchInput').value,'');
  assert.equal(app.document.getElementById('sortSelect').value,'latest');
  assert.equal(app.evaluate('currentPage'),1);
  assert.equal(app.evaluate('filteredProducts.length'),8);
});
add('JS34_filter_controls_update_internal_state_and_reset_page', () => {
  const app = catalogue();
  app.document.getElementById('categoryFilter').value='cu';
  app.document.getElementById('priceFilter').value='0-50000';
  app.document.getElementById('sortSelect').value='price_asc';
  app.evaluate('currentPage=2; handleFilterChange()'); app.flush();
  assert.equal(app.evaluate('currentPage'),1);
  assert.deepEqual(plain(app.evaluate('filteredProducts.map(p=>p.id)')),[1]);
});
add('JS35_update_ui_filter_visibility_and_count', () => {
  const app = catalogue(); app.evaluate('updateUI()');
  assert.equal(app.document.getElementById('clearFiltersRow').style.display,'none');
  app.filters({search:'rốt'}); app.evaluate('filteredProducts=filterProducts(allProducts); updateUI()');
  assert.equal(app.document.getElementById('clearFiltersRow').style.display,'block');
  assert.ok(app.document.getElementById('productCount').textContent.includes('1 sản phẩm'));
});
add('JS36_loading_indicator_both_branches', () => {
  const app = catalogue(); app.evaluate('showLoading(true)');
  assert.equal(app.document.getElementById('loadingSpinner').style.display,'block');
  app.evaluate('showLoading(false)');
  assert.equal(app.document.getElementById('loadingSpinner').style.display,'none');
  assert.equal(app.document.getElementById('productsGrid').style.opacity,'1');
});
add('JS37_debounce_keeps_only_last_call', () => {
  const app = catalogue(); app.evaluate('var calls=[]; var f=debounce(x=>calls.push(x),500); f(1); f(2)');
  app.flush(); assert.deepEqual(plain(app.evaluate('calls')),[2]);
});

for (const [id, file, control, field] of [
  ['JS38','login','togglePassword','password'],
  ['JS39','register','togglePassword','password'],
  ['JS40','register','togglePasswordConfirm','password_confirmation'],
]) {
  add(id+'_password_toggle_algorithm_'+file+'_'+field, () => {
    const app = environment('resources/views/auth/'+file+'.blade.php');
    const button=app.document.getElementById(control), input=app.document.getElementById(field);
    assert.equal(typeof button.events.click,'function');
    button.events.click.call(button); assert.equal(input.type,'text');
    button.events.click.call(button); assert.equal(input.type,'password');
  });
}

function cartRow(quantity, price, complete=true) {
  const input={value:String(quantity)}, unit={textContent:price}, total={textContent:''};
  return { total, querySelector(selector) {
    if (!complete) return null;
    return selector==='.quantity-input'?input:selector==='.item-total'?total:unit;
  }};
}
add('JS41_cart_display_sums_two_rows_after_quantity_change_or_removal', () => {
  const app=environment('resources/views/cart/index.blade.php');
  const first=cartRow(2,'15.000đ / kg'), second=cartRow(1,'20.000đ / kg');
  app.document.querySelectorAll=()=>[first,second]; app.evaluate('updateCartDisplay()');
  assert.equal(first.total.textContent,'30.000đ');
  assert.equal(app.document.querySelector('.total-amount').textContent,'50.000đ');
  app.document.querySelectorAll=()=>[second]; app.evaluate('updateCartDisplay()');
  assert.equal(app.document.querySelector('.total-amount').textContent,'20.000đ');
});
add('JS42_cart_display_empty_rows_reset_total_and_ignore_incomplete_row', () => {
  const app=environment('resources/views/cart/index.blade.php');
  app.document.querySelectorAll=()=>[cartRow(2,'15.000đ / kg',false)];
  app.evaluate('updateCartDisplay()');
  assert.equal(app.document.querySelector('.total-amount').textContent,'0đ');
});

// Additional acceptance assumption: searching without Vietnamese accents should
// match accented names. The report mentions this input but does not define matching.
add('JR01_unaccented_search_matches_accented_name_IF_required', () => {
  const app=catalogue(); app.filters({search:'ca rot'});
  assert.deepEqual(app.ids(),[1], 'Needs accent normalization only IF this is the agreed requirement.');
}, 'requirements');

const selected=cases.filter(item=>suite==='all'||item.group===suite);
const results=[];
for (const item of selected) {
  const started=performance.now();
  try {
    item.run(); results.push({name:item.name, status:'PASS', milliseconds:performance.now()-started});
    process.stdout.write('PASS '+item.name+'\n');
  } catch(error) {
    results.push({name:item.name,status:'FAIL',milliseconds:performance.now()-started,message:error.message});
    process.stdout.write('FAIL '+item.name+'\n  '+error.message+'\n');
  }
}
const passed=results.filter(item=>item.status==='PASS').length;
const report={
  generated_at:new Date().toISOString(), node:process.version, suite,
  environment:'Node VM with a minimal DOM adapter; not a real browser',
  project_root:projectRoot, total:results.length, passed, failed:results.length-passed, results,
};
const output=path.resolve(option('--output', 'reports/whitebox-js-'+suite+'.json'));
fs.mkdirSync(path.dirname(output),{recursive:true});
fs.writeFileSync(output,JSON.stringify(report,null,2));
process.stdout.write(`TOTAL ${report.total}; PASS ${report.passed}; FAIL ${report.failed}\nReport: ${output}\n`);
process.exitCode=report.failed?1:0;
