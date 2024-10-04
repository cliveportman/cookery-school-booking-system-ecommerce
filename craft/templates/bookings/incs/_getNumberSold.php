{% set orders = craft.commerce.orders.hasPurchasables([variant]) %}
{% set numberSold = 0 %}
{% for order in orders %}
  {% for lineItem in order.lineItems %}
    {% if lineItem.purchasable.id == variant.id %}
      {% set numberSold = numberSold + lineItem.qty %}
    {% endif %}                        
  {% endfor %}
{% endfor %}