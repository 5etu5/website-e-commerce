<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

function get_product($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

if($action==='add'){
    $product_id = $data['product_id'];
    $size = $data['size'];
    $quantity = intval($data['quantity'] ?? 1);
    $key = $product_id . '_' . $size;

    if(isset($_SESSION['cart'][$key])) $_SESSION['cart'][$key]['quantity'] += $quantity;
    else {
        $product = get_product($product_id);
        if(!$product) { echo json_encode(['success'=>false,'message'=>'Product not found']); exit; }
        $_SESSION['cart'][$key] = [
            'id'=>$product['id'],
            'name'=>$product['name'],
            'price'=>$product['price'],
            'image_url'=>$product['image_url'],
            'size'=>$size,
            'quantity'=>$quantity
        ];
    }
    echo json_encode(['success'=>true]);
    exit;
}

if($action==='update'){
    $key = $data['key'];
    $quantity = max(1,intval($data['quantity']??1));
    if(isset($_SESSION['cart'][$key])){
        $_SESSION['cart'][$key]['quantity']=$quantity;
        echo json_encode(['success'=>true]);
        exit;
    }
}

if($action==='remove'){
    $key = $data['key'];
    if(isset($_SESSION['cart'][$key])){
        unset($_SESSION['cart'][$key]);
        echo json_encode(['success'=>true]);
        exit;
    }
}

echo json_encode(['success'=>false,'message'=>'Invalid request']);
