<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds for GNews Intel.
     */
    public function run(): void
    {
        $newsItems = [
            [
                'country_code' => 'CN',
                'title'        => 'China Port Congestion Eases as Maritime Container Throughput Hits Record Highs',
                'summary'      => 'Major Chinese ports including Shanghai and Ningbo report smoother container processing times despite elevated trade volume.',
                'content'      => 'Throughput in Shanghai container terminals expanded by 4.2% year-over-year, improving efficiency across Pacific trade routes.',
                'source'       => 'Reuters Maritime',
                'url'          => 'https://www.reuters.com/business/logistics',
                'image'        => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'sentiment'    => 'Positive',
                'impact_score' => 25,
                'category'     => 'Logistics',
                'published_at' => now()->subHours(3),
            ],
            [
                'country_code' => 'DE',
                'title'        => 'German Industrial Export Orders Fall Amid Rising Energy & Raw Material Prices',
                'summary'      => 'Factory output across Germany faces headwinds from inflation and supply chain delays in critical manufacturing components.',
                'content'      => 'Manufacturing managers report persistent bottlenecks for specialized microchips and raw metals imported from Asian partners.',
                'source'       => 'Financial Times',
                'url'          => 'https://www.ft.com/world/europe',
                'image'        => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80',
                'sentiment'    => 'Negative',
                'impact_score' => 65,
                'category'     => 'Economic',
                'published_at' => now()->subHours(6),
            ],
            [
                'country_code' => 'ID',
                'title'        => 'Indonesia Expands Logistics Corridor Infrastructure at Port of Tanjung Priok',
                'summary'      => 'Government unveils upgraded container terminal capacity to streamline ASEAN maritime shipping routes.',
                'content'      => 'The new terminal expansion adds 1.5M TEU annual capacity, reducing port wait times for inter-island and international cargo vessels.',
                'source'       => 'Jakarta Post Freight',
                'url'          => 'https://www.thejakartapost.com/business',
                'image'        => 'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&w=800&q=80',
                'sentiment'    => 'Positive',
                'impact_score' => 20,
                'category'     => 'Logistics',
                'published_at' => now()->subHours(10),
            ],
            [
                'country_code' => 'US',
                'title'        => 'US West Coast Port Authorities Monitor Potential Dockworker Contract Negotiations',
                'summary'      => 'Logistics planners prepare contingency routing as labor unions and terminal operators discuss new employment terms.',
                'content'      => 'Importers are proactively diversifying shipments to East Coast ports to mitigate potential delays at Los Angeles and Long Beach hubs.',
                'source'       => 'Bloomberg Supply Chain',
                'url'          => 'https://www.bloomberg.com/news',
                'image'        => 'https://images.unsplash.com/photo-1559297434-fae8a1916a79?auto=format&fit=crop&w=800&q=80',
                'sentiment'    => 'Neutral',
                'impact_score' => 45,
                'category'     => 'Political',
                'published_at' => now()->subHours(18),
            ],
            [
                'country_code' => 'SG',
                'title'        => 'Singapore Bunkering Hub Outlines Strategic Fuel Reserve to Counter Supply Shocks',
                'summary'      => 'Port of Singapore Authority ensures stable bunker fuel supply for commercial shipping fleets operating along Malacca Strait.',
                'content'      => 'Strategic maritime reserves will guarantee continuous refueling operations for global cargo lines during geopolitical uncertainties.',
                'source'       => 'Singapore Logistics News',
                'url'          => 'https://www.straitstimes.com/business',
                'image'        => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80',
                'sentiment'    => 'Positive',
                'impact_score' => 15,
                'category'     => 'Security',
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($newsItems as $nData) {
            $country = Country::where('country_code', $nData['country_code'])->first();
            
            News::updateOrCreate(
                ['title' => $nData['title']],
                [
                    'country_id'   => $country ? $country->id : null,
                    'summary'      => $nData['summary'],
                    'content'      => $nData['content'],
                    'source'       => $nData['source'],
                    'url'          => $nData['url'],
                    'image'        => $nData['image'],
                    'sentiment'    => $nData['sentiment'],
                    'impact_score' => $nData['impact_score'],
                    'category'     => $nData['category'],
                    'published_at' => $nData['published_at'],
                ]
            );
        }
    }
}
