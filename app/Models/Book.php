<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;

    public  function reviews(){
        return $this->hasMany(Review::class);
    }

    // local querry scope
    public function scopeTitle(Builder $query, string $title): Builder {

        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopePopular(Builder $query, $from = null, $to = null):Builder|QueryBuilder {
        return $query->withCount(['reviews' =>  fn(Builder $q) => $this ->dateRangeFilter($q, $from, $to)]
        )->orderBy('reviews_count','desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder {
        return $query -> withAvg (['reviews' =>  fn(Builder $q) => $this ->dateRangeFilter($q, $from, $to)] , 'rating')->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder |QueryBuilder {
        return $query -> having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null){

            if($from && !$to){
                $query-> where('created_at', '>=', $from);
            } elseif (!$from && $to) {
                $query-> where('created_at', '<=', $to);
            } elseif ($from && $to) {
                $query ->whereBetween('created_at', [$from, $to]);
            }
        }

    public function scopePopularLastMonth(Builder $query): Builder |QueryBuilder {
        return $query-> popular(now()->subMonth(), now())->highestRated(now()->subMonth(), now())->minReviews(2);
    }
    public function scopePopularLast6Months(Builder $query): Builder |QueryBuilder {
        return $query-> popular(now()->subMonth(6), now())->highestRated(now()->subMonth(6), now())->minReviews(2);
    }
      public function scopeHighestRatedLastMonth(Builder $query): Builder|QueryBuilder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder|QueryBuilder
    {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }




    // :Builder specifies the return type from the query builder
    // lets say you intend to be more specific with the data you want from the database you can also use the query built like this:
    //     \App\Models\Book::title('delectus')->where('created_at', '>', '2023-01-01')->get();
    //     to know the sql query applied under the hood use toSql();
    // Builder|QueryBuilder: a union return type
}
