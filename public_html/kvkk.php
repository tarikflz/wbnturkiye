<?php
require __DIR__ . '/_app.php';

layout_start([
    'title' => 'WBN Türkiye | KVKK ve Gizlilik',
    'description' => 'WBN Türkiye KVKK ve Gizlilik metni',
    'css' => ['pages/kvkk'],
    'active' => 'kvkk',
]);
?>
<section class="page-hero">
  <div class="container hero-grid">
    <div>
      <img class="brand-logo" src="data:image/png;base64,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" alt="WBN Türkiye logosu">
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

<main class="privacy-section">
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
