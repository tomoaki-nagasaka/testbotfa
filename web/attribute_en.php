<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>Attribute registration</title>
</head>
<body>
<p>Please select a language.</p>
<select id="language" onChange="lchange()">
<option value="01">日本語</option>
<option value="02" selected>English</option>
</select>
<p>Please select your age.</p>
<select id="age">
<option value="999" selected>Age</option>
<option value="0">0</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
<option value="31">31</option>
<option value="32">32</option>
<option value="33">33</option>
<option value="34">34</option>
<option value="35">35</option>
<option value="36">36</option>
<option value="37">37</option>
<option value="38">38</option>
<option value="39">39</option>
<option value="40">40</option>
<option value="41">41</option>
<option value="42">42</option>
<option value="43">43</option>
<option value="44">44</option>
<option value="45">45</option>
<option value="46">46</option>
<option value="47">47</option>
<option value="48">48</option>
<option value="49">49</option>
<option value="50">50</option>
<option value="51">51</option>
<option value="52">52</option>
<option value="53">53</option>
<option value="54">54</option>
<option value="55">55</option>
<option value="56">56</option>
<option value="57">57</option>
<option value="58">58</option>
<option value="59">59</option>
<option value="60">60</option>
<option value="61">61</option>
<option value="62">62</option>
<option value="63">63</option>
<option value="64">64</option>
<option value="65">65</option>
<option value="66">66</option>
<option value="67">67</option>
<option value="68">68</option>
<option value="69">69</option>
<option value="70">70</option>
<option value="71">71</option>
<option value="72">72</option>
<option value="73">73</option>
<option value="74">74</option>
<option value="75">75</option>
<option value="76">76</option>
<option value="77">77</option>
<option value="78">78</option>
<option value="79">79</option>
<option value="80">80</option>
<option value="81">81</option>
<option value="82">82</option>
<option value="83">83</option>
<option value="84">84</option>
<option value="85">85</option>
<option value="86">86</option>
<option value="87">87</option>
<option value="88">88</option>
<option value="89">89</option>
<option value="90">90</option>
<option value="91">91</option>
<option value="92">92</option>
<option value="93">93</option>
<option value="94">94</option>
<option value="95">95</option>
<option value="96">96</option>
<option value="97">97</option>
<option value="98">98</option>
<option value="99">99</option>
<option value="100">100</option>
<option value="101">101</option>
<option value="102">102</option>
<option value="103">103</option>
<option value="104">104</option>
<option value="105">105</option>
<option value="106">106</option>
<option value="107">107</option>
<option value="108">108</option>
<option value="109">109</option>
<option value="110">110</option>
<option value="111">111</option>
<option value="112">112</option>
<option value="113">113</option>
<option value="114">114</option>
<option value="115">115</option>
<option value="116">116</option>
<option value="117">117</option>
<option value="118">118</option>
<option value="119">119</option>
<option value="120">120</option>
</select>
<p>Please select sex.</p>
<select id="sex">
<option value="0" selected>Sex</option>
<option value="1">Male</option>
<option value="2">Female</option>
</select>
<p>Please select your region of residence.</p>
<select id="region">
<option value="000" selected>Region</option>
<option value="001">East District</option>
<option value="002">West District</option>
<option value="003">Middle District</option>
<option value="004">South District</option>
<option value="005">North District</option>
</select>
<br>
<input type="button" onclick="clearform()" value="Clear" />
<input type="button" onclick="update()" value="Update" />
<input type="button" onclick="del()" value="Delete" />

<?php

//引数
$user = $_GET['user'];


?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script>
var param = "";
var user = "";
var lang = "";
var age = "";
var sex = "";
var region = "";

$(function() {
	var param = <?php echo json_encode($user); ?>;
	user = param.substr(0,1) + param.substr(2,1) + param.substr(6,1) + param.substr(10)
	sex = param.substr(1,1);
	age = param.substr(3,3);
	region = param.substr(7,3);
	age = Number(age);

	//alert("sex:" + sex + " age:" + age + " region:" + region);

	document.getElementById('age').value = age;
	document.getElementById('sex').value = sex;
	document.getElementById('region').value = region;
});

//言語選択
function lchange(){
	if(document.getElementById('language').value == "01"){
		location.href = "https://gyoseibot.herokuapp.com/attribute.php?user=" + user;
	}
}

//クリア
function clearform(){
	document.getElementById('language').selectedIndex = 0;
	document.getElementById('age').selectedIndex = 0;
	document.getElementById('sex').selectedIndex = 0;
	document.getElementById('region').selectedIndex = 0;
}

//更新
function update(){
	lang = document.getElementById('language').value;
	age = document.getElementById('age').value;
	sex = document.getElementById('sex').value;
	region = document.getElementById('region').value;
	$.ajax({
		type: "POST",
		url: "userinfoup.php",
		data: {
			"user" : user,
			"lang" : lang,
			"age" : age,
			"sex" : sex,
			"region" : region
		}
	}).then(
		function(){
			alert("Registration has been completed. Please close the screen.");
		},
		function(){
			alert("Registration failed.");
		}
	);
}

//削除
function del(){
	$.ajax({
		type: "POST",
		url: "userinfodel.php",
		data: {
			"user" : user
		}
	}).then(
		function(){
			alert("Deletion is complete. Please close the screen.");
		},
		function(){
			alert("Delete failed.");
		}
	);
}
</script>
</body>
</html>

