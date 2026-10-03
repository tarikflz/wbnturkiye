<?php
declare(strict_types=1);

/**
 * Basit form doğrulayıcı. Kural örnekleri:
 *   'full_name' => ['required', 'max:150']
 *   'email'     => ['required', 'email']
 *   'linkedin'  => ['required', 'url']
 *   'sector'    => ['required', 'in:Finans,Teknoloji']
 *   'expertise' => ['array', 'in:...']   (çoklu seçim)
 *   'kvkk'      => ['accepted']
 * Geçerli veriyi döner; hata varsa 422 HttpError fırlatır.
 */
function validate(array $input, array $rules): array
{
    $clean = [];
    $errors = [];

    foreach ($rules as $field => $fieldRules) {
        $isArray = in_array('array', $fieldRules, true);
        $value = $input[$field] ?? ($isArray ? [] : '');

        if ($isArray) {
            $value = is_array($value) ? array_values(array_unique(array_map(fn ($v) => trim((string) $v), $value))) : [];
            $value = array_values(array_filter($value, fn ($v) => $v !== ''));
        } else {
            $value = is_array($value) ? '' : trim((string) $value);
            $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
        }

        foreach ($fieldRules as $rule) {
            [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
            $empty = $isArray ? $value === [] : $value === '';

            $error = match ($name) {
                'required' => $empty ? 'Bu alan zorunludur.' : null,
                'accepted' => $empty ? 'Devam etmek için onay vermelisiniz.' : null,
                'max' => !$isArray && mb_strlen($value) > (int) $arg ? "En fazla $arg karakter olabilir." : null,
                'email' => !$empty && !filter_var($value, FILTER_VALIDATE_EMAIL) ? 'Geçerli bir e-posta adresi girin.' : null,
                'url' => !$empty && !is_http_url($value) ? 'Geçerli bir adres girin (https:// ile başlayan).' : null,
                'in' => !$empty && array_diff($isArray ? $value : [$value], explode('|', (string) $arg)) ? 'Geçersiz seçim.' : null,
                default => null,
            };

            if ($error !== null) {
                $errors[$field] = $error;
                break;
            }
        }

        $clean[$field] = $value;
    }

    if ($errors) {
        throw new HttpError(422, 'Lütfen işaretli alanları kontrol edin.', $errors);
    }
    return $clean;
}

function is_http_url(string $value): bool
{
    return (bool) filter_var($value, FILTER_VALIDATE_URL)
        && in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
}
