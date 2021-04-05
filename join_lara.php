
<?php
Create SQL Join Statement in Laravel Controller

Controller

$users = DB::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

$data = DB::table('tbl_customer')
                ->join('tbl_stock', 'customer_id', '=', 'tbl_customer.customer_id')
                ->select('tbl_customer.*', 'tbl_stock.*')
                ->where('customer_id', '=', 1) 
                ->get();

============================================================================
$data = DB::table('tbl_customer')
            ->join ......  //Im not sure about this 
            ->select ....  // neither this 
            ->get();

            print_r($data)

Inner Join Clause
The query builder may also be used to write join statements. To perform a basic "inner join", you may use the join method on a query builder instance. The first argument passed to the join method is the name of the table you need to join to, while the remaining arguments specify the column constraints for the join. You can even join to multiple tables in a single query:

$users = DB::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

Left Join / Right Join Clause
If you would like to perform a "left join" or "right join" instead of an "inner join", use the leftJoin or rightJoin methods. These methods have the same signature as the join method:

$users = DB::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

$users = DB::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

Cross Join Clause
To perform a "cross join" use the crossJoin method with the name of the table you wish to cross join to. Cross joins generate a cartesian product between the first table and the joined table:

$users = DB::table('sizes')
            ->crossJoin('colours')
            ->get();
Advanced Join Clauses
You may also specify more advanced join clauses. To get started, pass a Closure as the second argument into the join method. The Closure will receive a JoinClause object which allows you to specify constraints on the join clause:

DB::table('users')
        ->join('contacts', function ($join) {
            $join->on('users.id', '=', 'contacts.user_id')->orOn(...);
        })
        ->get();
If you would like to use a "where" style clause on your joins, you may use the where and orWhere methods on a join. Instead of comparing two columns, these methods will compare the column against a value:

DB::table('users')
        ->join('contacts', function ($join) {
            $join->on('users.id', '=', 'contacts.user_id')
                 ->where('contacts.user_id', '>', 5);
        })
        ->get();
Sub-Query Joins
You may use the joinSub, leftJoinSub, and rightJoinSub methods to join a query to a sub-query. Each of these methods receive three arguments: the sub-query, its table alias, and a Closure that defines the related columns:

$latestPosts = DB::table('posts')
                   ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
                   ->where('is_published', true)
                   ->groupBy('user_id');

$users = DB::table('users')
        ->joinSub($latestPosts, 'latest_posts', function ($join) {
            $join->on('users.id', '=', 'latest_posts.user_id');
        })->get();
Unions
The query builder also provides a quick way to "union" two queries together. For example, you may create an initial query and use the union method to union it with a second query:

$first = DB::table('users')
            ->whereNull('first_name');

$users = DB::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();




