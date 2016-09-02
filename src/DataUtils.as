package
{
	public class DataUtils
	{
		
		[Bindable(event="dataChanged")]
		public static function getBoundIndex ( dataProvider:XMLList, attributeName:String, searchValue:String ):Number {
			
			for ( var i:Number = 0; i < dataProvider.length(); i++ ){
				
				var xml:XML = dataProvider[ i ];
				
				if ( xml.attribute( attributeName ) == searchValue ){
					
					return i;
					
				}
				
			}
			
			return -1;
		}
		
	}
}