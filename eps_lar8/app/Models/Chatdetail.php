<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Chatdetail extends Model
{
    protected $table = 'chat_detail';
    protected $primaryKey = 'id'; 

    public function getChatByIdshop($id_shop){
    $query=DB::table('chat')
        ->select('*')
        ->where('id_shop',$id_shop)
        ->get();

        $chat = array();
        foreach ($query as $data) {
            $chat[$data->id] = $data;
			$chat[$data->id]->detail = $this->_getChatByIdshop($data->id);
        }
        return $chat;
    }

    private function _getChatByIdshop($id_chat) {
        return DB::table('chat_detail')
            ->select(DB::raw("ROUND(AVG(DATEDIFF(
                (SELECT created_date FROM chat_detail WHERE id_chat = ? AND send_by = 'seller' ORDER BY id DESC LIMIT 1),
                (SELECT created_date FROM chat_detail WHERE id_chat = ? AND id_product IS NOT NULL ORDER BY id DESC LIMIT 1)
            ))) AS averange_time"))
            ->where('id_chat', $id_chat)
            ->setBindings([$id_chat, $id_chat, $id_chat])  
            ->first();
    }
    
    

    public function get_percentage_chat($id_shop){
        $chats = $this->getChatByIdshop($id_shop);
        if (count($chats)> 0) {
            $total_data = count($chats);
    
            foreach ($chats as $chat) {
                $array_id[] = $chat->id;
            }
    
            $not_replied = self::whereIn('id_chat', $array_id)
                ->select('id_chat')
                ->where('send_by', 'seller')
                ->groupBy('id_chat')
                ->get();
            
            $not_replied_chat= count($not_replied);
            $total = ($not_replied_chat / $total_data) * 100;
    
            return $total;
        }
        // return dd($total_data);
    }
    
    // public function getChatSummary($id_shop) {
    //     $chats = $this->getChatByIdshop($id_shop);
    //     if (count($chats)> 0) { 
    //         $avg = 0;
    //         foreach ($chats as $chat) {
    //             $average = 0;
    //             foreach ($chat->detail as $detail) {
    //                 $averangeTime = $detail->averange_time;
    //                 $cek= $averangeTime;
    //                 // $average += $average + $cd->averange_time;
    //             }

    //             // $avg += $avg + (count($chat->detail) / $average);
    //         }
    //         // $total = count($chats) / $avg;

    //         return $cek;
    //     }else {
	// 		return 0;
	// 	}
    // }

    public function getChatSummary($id_shop) {
        $chats = $this->getChatByIdshop($id_shop);
        if (count($chats) > 0) {
            $totalAverageTime = 0;
            $totalDetailCount = 0;
    
            foreach ($chats as $chat) {
                $chatAverageTime = 0;
                $detailCount = 0;
    
                if (is_array($chat->detail)) {
                    foreach ($chat->detail as $detail) {
                        if (is_object($detail) && property_exists($detail, 'averange_time')) {
                            $chatAverageTime += $detail->averange_time;
                            $detailCount++;
                        }
                    }
    
                    if ($detailCount > 0) {
                        // Hitung rata-rata waktu untuk setiap chat
                        $chatAverageTime /= $detailCount;
                    }
                }
    
                // Tambahkan rata-rata waktu dari setiap chat ke total
                $totalAverageTime += $chatAverageTime;
                $totalDetailCount += $detailCount;
            }
    
            // Hitung rata-rata total jika totalDetailCount lebih dari 0
            if ($totalDetailCount > 0) {
                return $totalAverageTime / $totalDetailCount;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    
}