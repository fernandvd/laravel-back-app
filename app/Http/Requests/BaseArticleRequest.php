<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\{Arr, Str};
use Illuminate\Validation\Rule;
use App\Models\Article;

class BaseArticleRequest extends FormRequest
{


    public function prepateForValidation(): void 
    {
        $input = $this->input();
        $title = Arr::get($input, 'article.title');

        if (is_string($title)) {
            Arr::set($input, 'article.slug', Str::slug($title));
        } else {
            Arr::forget($input, 'article.slug');
        }

        $this->merge($input);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $article = Article::whereSlug($this->route('slug'))->first();

        $unique = Rule::unique('articles', 'slug');
        if ($article !== null) {
            $unique->ignoreModel($article);
        }
        return [
            'title' => ['string', 'max:255'],
            'slug' => ['string', 'max:255', $unique],
            'description' => ['string', 'max:510'],
            'body' => ['string', ],
        ];
    }

    public function validationData()
    {
        return Arr::wrap($this->input('article'));
    }
}
