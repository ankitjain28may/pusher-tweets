<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TwitterStreamingApi;
use App\Events\Tweets;

class TweetsStream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tweets:stream';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stream the tweets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        TwitterStreamingApi::publicStream()
            ->whenHears('#twilio', function(array $tweet) {
                $tweet_data = [
                    'text' => $tweet['text'],
                    'user_name' => $tweet['user']['screen_name'],
                    'name' => $tweet['user']['name'],
                    'profile_image_url_https' => $tweet['user']['profile_image_url_https'],
                    'retweet_count' => $tweet['retweet_count'],
                    'reply_count' => $tweet['reply_count'],
                    'favorite_count' => $tweet['favorite_count'],
                ];
                if (isset($tweet['extended_tweet'])) {
                    $tweet_data['text'] = $tweet['extended_tweet']['full_text'];
                }

                if (isset($tweet['created_at'])) {
                    $tweet_data['date'] = date("M d", strtotime($tweet['created_at']));
                }

                if (isset($tweet['extended_entities']['media'][0]['media_url'])) {
                    $tweet_data['image'] = $tweet['extended_entities']['media'][0]['media_url'];
                }
                broadcast(new Tweets($tweet_data))->toOthers();
            })
            ->startListening();
    }
}
