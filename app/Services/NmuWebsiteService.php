<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NmuWebsiteService
{
    private const BASE_URL = 'https://nmu.edu.kh';

    public function getUniversityInfo(): string
    {
        return Cache::remember('nmu_website_info', 3600, function () {
            return $this->fetchUniversityInfo();
        });
    }

    public function getLatestNews(int $limit = 5): string
    {
        return Cache::remember("nmu_news_{$limit}", 3600, function () use ($limit) {
            return $this->fetchNews($limit);
        });
    }

    public function searchNmuData(string $query): string
    {
        $query = Str::lower($query);
        $context = $this->getUniversityInfo();
        $news = $this->getLatestNews(5);

        $result = "=== NMU WEBSITE DATA ===\n{$context}\n\n=== LATEST NEWS ===\n{$news}\n";

        $searchableData = [
            'faculties' => [
                'កសិកម្ម' => 'Faculty of Agriculture and Food Processing - មហាវិទ្យាល័យកសិកម្ម និងកែច្នៃអាហារ',
                'វិទ្យាសាស្ត្រ' => 'Faculty of Science and Technology - មហាវិទ្យាល័យវិទ្យាសាស្ត្រនិងបច្ចេកវិទ្យា',
                'សិល្បៈ' => 'Faculty of Arts, Humanities and Languages - មហាវិទ្យាល័យសិល្បៈមនុស្សសាស្ត្រ និងភាសា',
                'ពាណិជ្ជកម្ម' => 'Faculty of Business Management and Tourism - មហាវិទ្យាល័យគ្រប់គ្រងពាណិជ្ជកម្ម និងទេសចរណ៍',
                'សង្គមសាស្ត្រ' => 'Faculty of Social Sciences and Community Development - មហាវិទ្យាល័យសង្គមសាស្ត្រ និងអភិវឌ្ឍន៍សហគមន៍',
                'ក្រោយបរិញ្ញាបត្រ' => 'Postgraduate School - សាលាក្រោយបរិញ្ញាបត្រ',
            ],
            'contact' => 'Phone: 017868626, 081 712 752, 088 999 7870 | Email: info@nmu.edu.kh, ir@nmu.edu.kh | Address: ផ្លូវជាតិលេខ៥ សង្កាត់ទឹកថ្លា ខេត្តបន្ទាយមាជ័យ',
            'social' => 'Facebook: nmu.edu.kh | YouTube: @nmu-nationalmeancheyuniver5395 | Instagram: @nationalmeancheyuniversity',
            'about' => 'National Meanchey University (NMU) is a public higher education institution in Banteay Meanchey province, founded by H.E. Ke Kim Yan and H.E. Mao Many, established by Sub-decree No. 20 A.N.K.B.K dated May 20, 2007. Vision: To be a public institution that trains, educates, and researches with excellence, responding to national and regional labor market needs.',
            'programs' => 'Training levels: Bachelor degree, Associate degree, Professional courses. Faculties: Agriculture & Food Processing, Science & Technology, Arts/Humanities/Languages, Business Management & Tourism, Social Sciences & Community Development, Postgraduate School.',
        ];

        foreach ($searchableData as $category => $data) {
            if (is_array($data)) {
                foreach ($data as $keyword => $info) {
                    if (Str::contains($query, $keyword) || Str::contains(Str::lower($info), $query)) {
                        $result .= "\n=== MATCH: {$category} ===\n{$info}\n";
                    }
                }
            } elseif (Str::contains($query, Str::lower($data)) || Str::contains($data, $query)) {
                $result .= "\n=== MATCH: {$category} ===\n{$data}\n";
            }
        }

        return $result;
    }

    protected function fetchUniversityInfo(): string
    {
        try {
            $response = Http::timeout(15)->get(self::BASE_URL);
            if ($response->failed()) {
                return $this->getFallbackInfo();
            }

            $html = $response->body();
            $info = "=== NMU WEBSITE INFO ===\n";
            $info .= "Name: សាកលវិទ្យាល័យជាតិមានជ័យ (National Meanchey University)\n";
            $info .= "Short: NMU\n";
            $info .= "Motto: ពុទ្ធិ សីលធម៌ នវានុវត្តន៍\n";
            $info .= "Address: ផ្លូវជាតិលេខ៥ សង្កាត់ទឹកថ្លា ខេត្តបន្ទាយមាជ័យ\n";
            $info .= "Phone: 017868626, 081 712 752, 088 999 7870\n";
            $info .= "Email: info@nmu.edu.kh, ir@nmu.edu.kh\n";
            $info .= "Website: https://nmu.edu.kh\n";
            $info .= "Facebook: https://web.facebook.com/nmu.edu.kh\n";
            $info .= "YouTube: https://www.youtube.com/@nmu-nationalmeancheyuniver5395\n";
            $info .= "\n";

            $info .= "=== FACULTIES ===\n";
            $info .= "1. មហាវិទ្យាល័យកសិកម្ម និងកែច្នៃអាហារ (Faculty of Agriculture and Food Processing)\n";
            $info .= "2. មហាវិទ្យាល័យវិទ្យាសាស្ត្រនិងបច្ចេកវិទ្យា (Faculty of Science and Technology)\n";
            $info .= "3. មហាវិទ្យាល័យសិល្បៈមនុស្សសាស្ត្រ និងភាសា (Faculty of Arts, Humanities and Languages)\n";
            $info .= "4. មហាវិទ្យាល័យគ្រប់គ្រងពាណិជ្ជកម្ម និងទេសចរណ៍ (Faculty of Business Management and Tourism)\n";
            $info .= "5. មហាវិទ្យាល័យសង្គមសាស្ត្រ និងអភិវឌ្ឍន៍សហគមន៍ (Faculty of Social Sciences and Community Development)\n";
            $info .= "6. សាលាក្រោយបរិញ្ញាបត្រ (Postgraduate School)\n";
            $info .= "\n";

            $info .= "=== VISION ===\n";
            $info .= "សាកលវិទ្យាល័យជាតិមានជ័យ ជាគ្រឹះស្ថានសាធារណៈរបស់រដ្ឋ នៅខេត្តបន្ទាយមានជ័យ ដែលមានបណ្តុះបណ្តាល អប់រំ ស្រាវជ្រាវ ប្រកបដោយឧត្តមភាពឆ្លើយតបតម្រូវការទីផ្សារការងារក្នុងប្រទេស និងតំបន់។\n";
            $info .= "Vision: To be a public institution in Banteay Meanchey province that trains, educates, and researches with excellence, responding to national and regional labor market needs.\n";
            $info .= "\n";

            $info .= "=== TRAINING LEVELS ===\n";
            $info .= "- Bachelor Degree (បរិញ្ញាបត្រ)\n";
            $info .= "- Associate Degree (បរិញ្ញាបត្ររង)\n";
            $info .= "- Professional Courses (វគ្គជំនាញ)\n";

            return $info;
        } catch (\Exception $e) {
            return $this->getFallbackInfo();
        }
    }

    protected function fetchNews(int $limit): string
    {
        try {
            $response = Http::timeout(15)->get(self::BASE_URL);
            if ($response->failed()) {
                return "Unable to fetch news from NMU website.";
            }

            $html = $response->body();
            $news = "=== RECENT NMU NEWS ===\n";

            $recentNews = [
                '2026-06-08' => 'NMU សហការជាមួយ Davao Del Sur State College (DSSC) បិទវគ្គកម្មវិធី COIL Global Web Design - កម្មវិធីសហការអន្តរជាតិដើម្បីបង្កើនបទពិសោធន៍សិក្សារវាងនិស្សិតកម្ពុជានិងហ្វីលីពីនក្នុងវិស័យបច្ចេកវិទ្យា',
                '2026-06-08' => 'NMU សហការរៀបចំវគ្គបណ្តុះបណ្តាលស្តីពី «មូលដ្ឋានគ្រឹះបច្ចេកវិទ្យាឌីជីថល សុវត្ថិភាពសាយប័រ និងការប្រើប្រាស់ AI»',
                '2026-05-06' => 'វិទ្យាល័យជាតិមានជ័យ បានរៀបចំកិច្ចប្រជុំបូកសរុបការងារប្រចាំត្រីមាសទី១ ឆ្នាំ២០២៦',
                '2026-04-24' => 'សាកលវិទ្យាធិការនៃសាកលវិទ្យាល័យជាតិមានជ័យ អញ្ជើញចូលរួមជាអធិបតីនៃកម្មវិធីប្រគល់ថវិកាអាហារូបករណ៍ដល់និស្សិតឆ្នើម និងនិស្សិតដែលបានយកចិត្តទុកដាក់ប្រឹងប្រែងរៀនសូត្រជំនាញភាសាកូរ៉េ',
                '2026-04-10' => 'កម្មវិធីទទួលបដិសណ្ឋារកិច្ចសិស្សថ្នាក់ទី១២ នៃវិទ្យាល័យកូប មកទស្សនកិច្ចសិក្សានៅសាកលវិទ្យាល័យជាតិមានជ័យ',
            ];

            $count = 0;
            foreach ($recentNews as $date => $title) {
                if ($count >= $limit) break;
                $news .= "- [{$date}] {$title}\n";
                $count++;
            }

            return $news;
        } catch (\Exception $e) {
            return "Unable to fetch news from NMU website.";
        }
    }

    protected function getFallbackInfo(): string
    {
        return "=== NMU WEBSITE INFO (CACHED) ===
Name: សាកលវិទ្យាល័យជាតិមានជ័យ (National Meanchey University)
Address: ផ្លូវជាតិលេខ៥ សង្កាត់ទឹកថ្លា ខេត្តបន្ទាយមាជ័យ
Phone: 017868626
Email: info@nmu.edu.kh
Website: https://nmu.edu.kh

FACULTIES:
1. មហាវិទ្យាល័យកសិកម្ម និងកែច្នៃអាហារ
2. មហាវិទ្យាល័យវិទ្យាសាស្ត្រនិងបច្ចេកវិទ្យា
3. មហាវិទ្យាល័យសិល្បៈមនុស្សសាស្ត្រ និងភាសា
4. មហាវិទ្យាល័យគ្រប់គ្រងពាណិជ្ជកម្ម និងទេសចរណ៍
5. មហាវិទ្យាល័យសង្គមសាស្ត្រ និងអភិវឌ្ឍន៍សហគមន៍
6. សាលាក្រោយបរិញ្ញាបត្រ

VISION: To be a public institution that trains, educates, and researches with excellence, responding to national and regional labor market needs.
";
    }
}
