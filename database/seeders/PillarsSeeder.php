<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Pillar;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class PillarsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No user found. Please run UserSeeder first.');
            return;
        }

        $userId = $user->id;
        $now = now();

        $pillars = [
            [
                'name' => 'Artificial Intelligence',
                'description' => 'Machine learning, deep learning, and intelligent systems',
                'icon' => 'Brain',
                'color' => '#3498db',
                'sort_order' => 1,
                'domains' => [
                    [
                        'name' => 'Machine Learning',
                        'color' => '#2980b9',
                        'topics' => ['Supervised Learning', 'Unsupervised Learning', 'Reinforcement Learning', 'Feature Engineering']
                    ],
                    [
                        'name' => 'Deep Learning',
                        'color' => '#1abc9c',
                        'topics' => ['Neural Networks', 'CNN', 'RNN', 'Transformers']
                    ],
                    [
                        'name' => 'Natural Language Processing',
                        'color' => '#16a085',
                        'topics' => ['Text Classification', 'Sentiment Analysis', 'Named Entity Recognition', 'Machine Translation']
                    ],
                    [
                        'name' => 'Computer Vision',
                        'color' => '#27ae60',
                        'topics' => ['Image Classification', 'Object Detection', 'Image Segmentation', 'GANs']
                    ],
                    [
                        'name' => 'Explainable AI',
                        'color' => '#2ecc71',
                        'topics' => ['SHAP', 'LIME', 'Feature Importance', 'Model Interpretability']
                    ],
                ]
            ],
            [
                'name' => 'Data Science',
                'description' => 'Statistical analysis, visualization, and data-driven insights',
                'icon' => 'BarChart3',
                'color' => '#2ecc71',
                'sort_order' => 2,
                'domains' => [
                    [
                        'name' => 'Statistics',
                        'color' => '#27ae60',
                        'topics' => ['Probability', 'Hypothesis Testing', 'Regression Analysis', 'Bayesian Methods']
                    ],
                    [
                        'name' => 'Data Visualization',
                        'color' => '#16a085',
                        'topics' => ['Matplotlib', 'D3.js', 'Tableau', 'Interactive Dashboards']
                    ],
                    [
                        'name' => 'Big Data',
                        'color' => '#1abc9c',
                        'topics' => ['Hadoop', 'Spark', 'Data Pipelines', 'Distributed Computing']
                    ],
                ]
            ],
            [
                'name' => 'Research Methods',
                'description' => 'Scientific methodology and research design',
                'icon' => 'Microscope',
                'color' => '#9b59b6',
                'sort_order' => 3,
                'domains' => [
                    [
                        'name' => 'Qualitative Methods',
                        'color' => '#8e44ad',
                        'topics' => ['Interviews', 'Case Studies', 'Ethnography', 'Grounded Theory']
                    ],
                    [
                        'name' => 'Quantitative Methods',
                        'color' => '#9b59b6',
                        'topics' => ['Surveys', 'Experiments', 'Statistical Modeling', 'Meta-Analysis']
                    ],
                    [
                        'name' => 'Mixed Methods',
                        'color' => '#a569bd',
                        'topics' => ['Sequential Design', 'Concurrent Design', 'Integration Strategies']
                    ],
                ]
            ],
            [
                'name' => 'Software Engineering',
                'description' => 'Software development, architecture, and best practices',
                'icon' => 'Code',
                'color' => '#e67e22',
                'sort_order' => 4,
                'domains' => [
                    [
                        'name' => 'Web Development',
                        'color' => '#d35400',
                        'topics' => ['React', 'Laravel', 'Node.js', 'REST APIs']
                    ],
                    [
                        'name' => 'Mobile Development',
                        'color' => '#e67e22',
                        'topics' => ['React Native', 'Flutter', 'iOS', 'Android']
                    ],
                    [
                        'name' => 'DevOps',
                        'color' => '#f39c12',
                        'topics' => ['CI/CD', 'Docker', 'Kubernetes', 'Cloud Infrastructure']
                    ],
                ]
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Information security and protection systems',
                'icon' => 'Shield',
                'color' => '#e74c3c',
                'sort_order' => 5,
                'domains' => [
                    [
                        'name' => 'Network Security',
                        'color' => '#c0392b',
                        'topics' => ['Firewalls', 'IDS/IPS', 'Network Protocols', 'VPN']
                    ],
                    [
                        'name' => 'Cryptography',
                        'color' => '#e74c3c',
                        'topics' => ['Encryption', 'Hashing', 'Digital Signatures', 'Blockchain']
                    ],
                    [
                        'name' => 'Ethical Hacking',
                        'color' => '#ec7063',
                        'topics' => ['Penetration Testing', 'Vulnerability Assessment', 'Social Engineering', 'Exploit Development']
                    ],
                ]
            ],
        ];

        foreach ($pillars as $pillarData) {
            $domains = $pillarData['domains'];
            unset($pillarData['domains']);
            
            $pillar = Pillar::create(array_merge($pillarData, [
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));

            $this->command->info("Created pillar: {$pillar->name}");

            foreach ($domains as $domainData) {
                $topics = $domainData['topics'];
                unset($domainData['topics']);
                
                $domain = Domain::create(array_merge($domainData, [
                    'pillar_id' => $pillar->id,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));

                foreach ($topics as $topicName) {
                    Topic::create([
                        'domain_id' => $domain->id,
                        'pillar_id' => $pillar->id,
                        'user_id' => $userId,
                        'name' => $topicName,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        $this->command->info('Pillars seeding completed!');
    }
}
