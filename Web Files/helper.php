<?PHP
	function sessionCheck() {
		session_start();
		if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
			header ("Location: login.php");
			exit();
		}
	}
	
	function printOciError() {
		$e = oci_error();
		print htmlentities($e['message']);
		print "\n<pre>\n";
		print htmlentities($e['sqltext']);
		printf("\n%".($e['offset']+1)."s", "^");
		print  "\n</pre>\n";
	}
	
	function printQueryError($stid) {
		$e = oci_error($stid);
		print htmlentities($e['message']);
		print "\n<pre>\n";
		print htmlentities($e['sqltext']);
		printf("\n%".($e['offset']+1)."s", "^");
		print  "\n</pre>\n";
	}
	
	function getConn() {
		$user_name = 'adhart';
		$pass_word = 'Aug111995';
		$conn_string = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db1.chpc.ndsu.nodak.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))';
		$conn = oci_connect($user_name, $pass_word, $conn_string);
		return $conn;
	}