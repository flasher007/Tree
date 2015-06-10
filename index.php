<?

include 'Tree/Tree.php';

use \Tree\Tree;

$a = array(
    array(
        'id' => 1,
        'parent_id' => 0,
        'name' => 'Первый'
    ),
    array(
        'id' => 2,
        'parent_id' => 0,
        'name' => 'Второй'
    ),
    array(
        'id' => 3,
        'parent_id' => 1,
        'name' => 'Подпункт первого'
    ),
    array(
        'id' => 4,
        'parent_id' => 3,
        'name' => 'Подпункт подпункта'
    ),
);

$menu = new Tree();
$menu->organizeTree($a)->buildTree();

echo '<h4>1. Вывод дерева в консоли в виде текста:</h4>';
echo $menu;

echo '<h4>2. Взятие ветки от узла с идентификатором 1 и вывод в консоли:</h4>';
$item1 = $menu->getItemById(1);
echo $item1;

echo '<h4>3. Взятие пути до корня от узла с идентификатором 4</h4>';
$item4 = $menu->getItemById(4);
echo $item4->getPath();
