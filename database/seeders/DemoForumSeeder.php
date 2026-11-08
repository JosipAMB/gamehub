<?php

namespace Database\Seeders;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoForumSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::firstOrCreate(
            ['email' => 'gamehub-demo@example.test'],
            [
                'name' => 'GameHub testspeler',
                'username' => 'gamehub_demo',
                'password' => Hash::make(Str::random(64)),
            ],
        );

        $categories = [
            [
                'title' => '[TEST] Algemene gaming',
                'description' => 'Voorbeeldcategorie om onderwerpen en reacties te testen.',
                'topics' => [
                    [
                        'title' => '[TEST] Welke game speel je momenteel?',
                        'body' => 'Ik zoek inspiratie voor mijn volgende game. Wat speel jij op dit moment, en wat maakt die game leuk?',
                        'replies' => [
                            'Ik ben weer begonnen aan een RPG. Heerlijk om rustig de wereld te verkennen.',
                            'Co-op games zijn mijn favoriet; samen spelen blijft het leukst.',
                        ],
                    ],
                    [
                        'title' => '[TEST] Jullie favoriete gamegenre',
                        'body' => 'Welk genre speel je het liefst: RPG, actie, strategie, simulatie of iets anders?',
                        'replies' => [
                            'Strategiegames! Ik vind het leuk om verschillende tactieken uit te proberen.',
                            'Voor mij zijn het avonturengames met een goed verhaal.',
                        ],
                    ],
                ],
            ],
            [
                'title' => '[TEST] PC & hardware',
                'description' => 'Voorbeeldcategorie voor vragen over pc-gaming en hardware.',
                'topics' => [
                    [
                        'title' => '[TEST] Tips voor een eerste gaming-pc',
                        'body' => 'Waar moet je volgens jullie op letten als je voor het eerst een gaming-pc samenstelt?',
                        'replies' => [
                            'Begin met je budget en kies onderdelen die goed bij elkaar passen.',
                            'Vergeet niet ruimte in je budget te houden voor een goede monitor.',
                        ],
                    ],
                    [
                        'title' => '[TEST] Hoe houden jullie je setup netjes?',
                        'body' => 'Hebben jullie handige tips om kabels en accessoires op je bureau georganiseerd te houden?',
                        'replies' => [
                            'Klittenbandstrips werken goed en zijn makkelijk opnieuw te gebruiken.',
                            'Een kabelgoot onder het bureau scheelt hier een hoop rommel.',
                        ],
                    ],
                ],
            ],
            [
                'title' => '[TEST] Tips & vragen',
                'description' => 'Voorbeeldcategorie om tips en vragen uit te proberen.',
                'topics' => [
                    [
                        'title' => '[TEST] Jouw beste beginnerstip',
                        'body' => 'Welke tip had jij graag gekregen toen je net begon met gamen?',
                        'replies' => [
                            'Speel vooral wat je zelf leuk vindt; je hoeft niet elke populaire game te spelen.',
                            'Neem af en toe pauze en geniet van het spel.',
                        ],
                    ],
                    [
                        'title' => '[TEST] Samen spelen: online of op de bank?',
                        'body' => 'Speel je liever online met vrienden of samen op de bank?',
                        'replies' => [
                            'Samen op de bank, met snacks erbij, blijft speciaal.',
                            'Online is handig als iedereen ergens anders woont.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $thread = Thread::firstOrCreate(
                ['title' => $categoryData['title']],
                [
                    'description' => $categoryData['description'],
                    'user_id' => $demoUser->id,
                ],
            );

            foreach ($categoryData['topics'] as $topicData) {
                $topic = Topic::firstOrCreate(
                    [
                        'thread_id' => $thread->id,
                        'title' => $topicData['title'],
                    ],
                    [
                        'body' => $topicData['body'],
                        'user_id' => $demoUser->id,
                    ],
                );

                foreach ($topicData['replies'] as $replyBody) {
                    Reply::firstOrCreate(
                        [
                            'topic_id' => $topic->id,
                            'body' => $replyBody,
                        ],
                        ['user_id' => $demoUser->id],
                    );
                }
            }
        }
    }
}
