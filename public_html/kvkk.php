<?php
require __DIR__ . '/_app.php';

layout_start([
    'footer_cta' => false, // sayfada zaten katılım çağrısı/form var
    'title' => 'WBN Türkiye | KVKK ve Gizlilik',
    'description' => 'WBN Türkiye KVKK ve Gizlilik Politikası: kişisel verilerinizin hangi amaçlarla işlendiği, saklandığı ve haklarınız hakkında bilgi.',
    'css' => ['pages/kvkk'],
    'active' => 'kvkk',
]);
?>
<section class="page-hero">
  <div class="container hero-grid">
    <div>
      <img class="brand-logo" src="<?= e(asset('img/wbn-logo-white.png')) ?>" alt="WBN Türkiye logosu" width="188" height="94">
      <div class="eyebrow">WBN Türkiye</div>
      <h1>KVKK ve Gizlilik</h1>
      <p class="hero-copy">
        Kişisel verilerinizin güvenliği, gizliliği ve şeffaf biçimde işlenmesi WBN Türkiye için temel öncelikler arasındadır.
      </p>
    </div>

    <aside class="hero-note">
              <p>
        Bu metin; WBN Türkiye web sitesi, üyelik süreçleri, etkinlik iletişimi ve topluluk faaliyetleri kapsamında paylaşılan kişisel veriler hakkında bilgilendirme amacıyla hazırlanmıştır.
      </p>
    </aside>
  </div>
</section>

<main id="main" tabindex="-1" class="privacy-section">
  <div class="container privacy-shell">
    <nav class="side-nav" aria-label="KVKK ve Gizlilik başlıkları">
      <small>İçindekiler</small>
      <a href="#kapsam">Kapsam</a>
      <a href="#veriler">İşlenen Kişisel Veriler</a>
      <a href="#amaclar">İşleme Amaçları</a>
      <a href="#hukuki">Hukuki Sebepler</a>
      <a href="#aktarim">Veri Aktarımı</a>
      <a href="#toplama">Toplama Yöntemleri</a>
      <a href="#saklama">Saklama Süreleri</a>
      <a href="#guvenlik">Veri Güvenliği</a>
      <a href="#haklar">KVKK Kapsamındaki Haklarınız</a>
      <a href="#iletisim">İletişim</a>
    </nav>

    <article class="content-card">
      <div class="intro-block">
        <h2>WBN Türkiye KVKK Aydınlatma ve Gizlilik Metni</h2>
        <p>
          WBN Türkiye, topluluk üyeleri, aday üyeler, etkinlik katılımcıları, iş ortakları ve web sitesi ziyaretçilerine ait kişisel verilerin 6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) kapsamında hukuka uygun, ölçülü, güvenli ve şeffaf biçimde işlenmesine önem verir.
        </p>
      </div>

      <section class="policy-section" id="kapsam">
        <h3>1. Kapsam ve Veri Sorumlusu Hakkında</h3>
        <p>
          Bu metin; WBN Türkiye tarafından yürütülen üyelik, iletişim, etkinlik, networking, mentorluk, eğitim, sosyal etki, iş birliği ve topluluk yönetimi süreçlerinde işlenen kişisel verilere ilişkin genel bilgilendirme sunar.
        </p>
      </section>

      <section class="policy-section" id="veriler">
        <h3>2. İşlenen Kişisel Veriler</h3>
        <p>WBN Türkiye ile kurduğunuz ilişkiye göre aşağıdaki kişisel veri kategorileri işlenebilir:</p>
        <ul>
          <li>Kimlik ve iletişim bilgileri: ad, soyad, telefon numarası ve e-posta adresi.</li>
          <li>Profesyonel bilgiler: çalışılan kurum, ünvan, sektör, uzmanlık ve ilgi alanları.</li>
          <li>Dijital profil bilgileri: LinkedIn profili ve benzeri profesyonel bağlantılar.</li>
          <li>Topluluk katılım bilgileri: etkinlik katılımı, mentorluk, eğitim, konuşmacılık, networking, çalışma grupları ve sosyal etki projelerine ilişkin tercihler.</li>
          <li>İletişim ve talep içerikleri: form alanlarında paylaşılan mesaj, görüş, talep ve ek bilgiler.</li>
          <li>Teknik veriler: web sitesinin güvenliği ve işleyişi kapsamında IP adresi, cihaz ve tarayıcı bilgileri ile zorunlu teknik kayıtlar.</li>
        </ul>
      </section>

      <section class="policy-section" id="amaclar">
        <h3>3. Kişisel Verilerin İşlenme Amaçları</h3>
        <p>Kişisel verileriniz, işleme faaliyetinin niteliğine göre aşağıdaki amaçlarla kullanılabilir:</p>
        <ul>
          <li>Üyelik başvurularını ve mevcut üye bilgilerinin güncellenmesini yürütmek.</li>
          <li>WBN Türkiye topluluk veri tabanını güncel ve doğru tutmak.</li>
          <li>Etkinlik, eğitim, networking, mentorluk ve çalışma gruplarına ilişkin iletişimi yürütmek.</li>
          <li>Topluluk üyeleri arasındaki profesyonel iş birliği ve etkileşim süreçlerini desteklemek.</li>
          <li>İlgi ve uzmanlık alanlarına göre uygun içerik, etkinlik ve topluluk iletişimleri oluşturmak.</li>
          <li>Talep, öneri, geri bildirim ve iletişim mesajlarını yanıtlamak.</li>
          <li>Bilgi güvenliği, kayıt güvenliği ve sistem sürekliliğini sağlamak.</li>
          <li>Mevzuattan doğan yükümlülükleri yerine getirmek ve yetkili kurum taleplerini karşılamak.</li>
        </ul>
      </section>

      <section class="policy-section" id="hukuki">
        <h3>4. Kişisel Verilerin İşlenmesinin Hukuki Sebepleri</h3>
        <p>
          Kişisel veriler; KVKK’nın 5. ve gerektiğinde 6. maddelerinde yer alan hukuki işleme şartlarından uygun olanına dayanılarak işlenir. Bu kapsamda açık rıza, bir sözleşmenin kurulması veya ifası, hukuki yükümlülüklerin yerine getirilmesi, bir hakkın tesisi, kullanılması veya korunması ile temel hak ve özgürlüklerinize zarar vermemek kaydıyla meşru menfaat gibi hukuki sebepler gündeme gelebilir.
        </p>
        <div class="info-box">
          Her veri işleme faaliyeti için uygulanacak hukuki sebep, ilgili sürecin niteliğine göre ayrıca değerlendirilmelidir.
        </div>
      </section>

      <section class="policy-section" id="aktarim">
        <h3>5. Kişisel Verilerin Aktarılması</h3>
        <p>
          Kişisel verileriniz; yalnızca gerekli olduğu ölçüde, KVKK ve ilgili mevzuata uygun güvenlik önlemleri çerçevesinde hizmet sağlayıcılar, teknoloji altyapısı sağlayıcıları, etkinlik operasyon paydaşları, hukuki veya mali danışmanlar ve yetkili kamu kurumlarıyla paylaşılabilir.
        </p>
        <p>
          Yurt dışına veri aktarımı söz konusu olduğunda, KVKK’da düzenlenen aktarım şartları ve ilgili kurul kararları dikkate alınır.
        </p>
      </section>

      <section class="policy-section" id="toplama">
        <h3>6. Kişisel Verilerin Toplanma Yöntemleri</h3>
        <p>
          Veriler; web sitesi formları, üye bilgi güncelleme formları, e-posta iletişimi, etkinlik kayıtları, yüz yüze veya çevrim içi görüşmeler, profesyonel iletişim kanalları ve benzeri yöntemlerle elektronik ya da fiziki ortamda toplanabilir.
        </p>
      </section>

      <section class="policy-section" id="saklama">
        <h3>7. Saklama Süreleri</h3>
        <p>
          Kişisel veriler, ilgili işleme amacı için gerekli süre boyunca ve uygulanabilir yasal saklama süreleri çerçevesinde muhafaza edilir. İşleme amacı ortadan kalktığında ve saklamayı gerektiren başka bir hukuki sebep kalmadığında veriler silinir, yok edilir veya anonim hâle getirilir.
        </p>
      </section>

      <section class="policy-section" id="guvenlik">
        <h3>8. Kişisel Verilerin Güvenliği</h3>
        <p>
          WBN Türkiye, kişisel verilerin hukuka aykırı işlenmesini ve erişimini önlemek, verilerin güvenli biçimde saklanmasını sağlamak amacıyla uygun teknik ve idari tedbirler uygular. Erişim yetkilerinin sınırlandırılması, güvenli saklama, yetkili erişim, sistem güvenliği ve kayıt yönetimi bu yaklaşımın temel unsurları arasındadır.
        </p>
      </section>

      <section class="policy-section" id="haklar">
        <h3>9. KVKK Kapsamındaki Haklarınız</h3>
        <p>KVKK’nın 11. maddesi kapsamında kişisel verilerinizle ilgili olarak aşağıdaki haklara sahipsiniz:</p>
        <ul>
          <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme.</li>
          <li>Kişisel verileriniz işlenmişse buna ilişkin bilgi talep etme.</li>
          <li>İşleme amacını ve verilerin amacına uygun kullanılıp kullanılmadığını öğrenme.</li>
          <li>Kişisel verilerin aktarıldığı üçüncü kişileri öğrenme.</li>
          <li>Eksik veya hatalı işlenen kişisel verilerin düzeltilmesini isteme.</li>
          <li>KVKK’da öngörülen şartlar çerçevesinde kişisel verilerin silinmesini veya yok edilmesini isteme.</li>
          <li>Düzeltme, silme veya yok etme işlemlerinin verilerin aktarıldığı üçüncü kişilere bildirilmesini isteme.</li>
          <li>Münhasıran otomatik sistemler aracılığıyla analiz edilen veriler nedeniyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme.</li>
          <li>Kanuna aykırı işleme nedeniyle zarara uğramanız hâlinde zararın giderilmesini talep etme.</li>
        </ul>
      </section>

      <section class="policy-section" id="iletisim">
        <h3>10. Başvuru ve İletişim</h3>
        <p>
          KVKK kapsamındaki taleplerinizi, kimliğinizi ve talebinizin kapsamını açık biçimde belirterek WBN Türkiye’ye iletebilirsiniz. Başvurular, mevzuatta öngörülen usul ve süreler çerçevesinde değerlendirilir.
        </p>

        <div class="contact-box">
          <div>
            <strong>WBN Türkiye İletişim</strong>
            <span>wbuzznetwork@gmail.com</span>
          </div>
          <a class="contact-button" href="mailto:wbuzznetwork@gmail.com?subject=KVKK%20Başvurusu%20-%20WBN%20Türkiye">KVKK Başvurusu Yap</a>
        </div>

        <div class="info-box">
          Bu sayfa, WBN Türkiye’nin üyelik, iletişim ve topluluk süreçlerinde paylaşılan kişisel verilerin hangi amaçlarla işlendiği ve nasıl korunduğu hakkında bilgilendirme sunar.
        </div>
      </section>
    </article>
  </div>
</main>
<?php layout_end(); ?>
