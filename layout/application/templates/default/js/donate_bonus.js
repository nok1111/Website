$(document).ready(function(){
	
	// Настройки
	var bonus_enabled = true; // Включены ли бонусы
	var price_coin = 1; // цена одной монеты
	var price_format = ''; // валюта цены монет
	var coin_format = ''; // имя монеты

	// Блоки
	var countBlock = $('#count_input'); // Откуда считывать кол-во монет
	var sumInput = $('#sum_input'); // Куда записывать итоговую стоимость монет
	var bonusBlock = $('.donate-page__price > span > b'); // Блок с количеством монет
	var priceBlock = $('.global-form__line > button > span > b'); // Блок с ценой
	var bonusViewBlock = $('.donate-page__bonuses-item'); // Блок с бонусом для отображения
	
	// все бонусы в порядке возрастания - это важно!
	var bonus_sum = [ 10, 25, 50, 100]; // суммы от n монет
	var bonus_percent = [ 10, 15, 30, 50]; // бонусы + n процентов
	
	// Всё, что дальше - лучше не трогать.
	var count = parseInt(countBlock.val());
	bonusBlock.text( 0 + ' ' + coin_format );
	priceBlock.text( 0 + ' ' + price_format );
	sumInput.val(0);


	$(document).keyup('#donate_form', function(a){
		count = parseInt(countBlock.val());
		if(isNaN(count) || count < 0)
		{
			count = 0;
			//countBlock.val(count);
		}
		if(!bonus_enabled || count < bonus_sum[0])
		{
			priceBlock.text( ( count * price_coin ) + ' ' + price_format );
			sumInput.val(count * price_coin);
			count = Math.ceil(count * 1.0);
			if(bonusViewBlock.length){
				bonusViewBlock.removeClass('active');
			}	
		}
		else
		{
			for(let i = bonus_sum.length - 1; i >=0; i--)
				if(count >= bonus_sum[i])
				{
					priceBlock.text( ( count * price_coin ) + ' ' + price_format );
					sumInput.val(count * price_coin);
					count = Math.ceil(count * bonus_percent[i] / 100 + count)*10;
					if(bonusViewBlock.length){
						bonusViewBlock.removeClass('active');
						bonusViewBlock.eq(i).addClass('active');
					}
					break;
				}

		}
		bonusBlock.text( count + ' ' + coin_format );
	});
	
	bonusViewBlock.click(function(event){
		if(bonus_enabled && bonusViewBlock.length){
			var index = $(this).index();
			bonusViewBlock.removeClass('active');
			$(this).addClass('active');
			countBlock.val((bonus_sum[index] * price_coin));
			sumInput.val((bonus_sum[index] * price_coin));
			priceBlock.text( ( bonus_sum[index] * price_coin ) + ' ' + price_format );
			sumInput.val(bonus_sum[index] * price_coin);
			count = Math.ceil(bonus_sum[index] * bonus_percent[index] / 100 + bonus_sum[index]);
			bonusBlock.text( count + '' + coin_format*10 );
		}
	});
	
});