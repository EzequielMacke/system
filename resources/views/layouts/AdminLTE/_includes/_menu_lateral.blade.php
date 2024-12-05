<aside class="main-sidebar">
	<section class="sidebar">
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header" style="color:#fff;"> MENU <i class="fa fa-level-down"></i></li>
			<li class=" ">
				<a href="{{ route('home') }}" title="Dashboard"><i class="fa fa-dashboard"></i> <span>Tablero</span></a>
			</li>

			@if(Request::segment(1) === 'profile')
			<li class="{{ Request::segment(1) === 'profile' ? 'active' : null }}">
				<a href="{{ route('profile') }}" title="Profile"><i class="fa fa-user"></i> <span> PERFILES</span></a>
			</li>

			@endif
			<li class="treeview">
				<a href="#"><i class="fa fa-gear"></i><span>Configuracion</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					{{-- @if (Auth::user()->can('root-dev', '')) --}}
						<li class="{{ Request::segment(1) === 'config' && Request::segment(2) === null ? 'active' : null }}">
							<a href="{{ route('config') }}" title="App Config">
								<i class="fa fa-gear"></i> <span> Configuracion</span>
							</a>
						</li>
					{{-- @endif --}}
					<li class="user">
						<a href="{{ route('user') }}" title="Users">
							<i class="fa fa-user"></i> <span> Usuarios</span>
						</a>
					</li>
                    <li class="user">
						<a href="{{ route('user') }}" title="Users">
							<i class="fa fa-user"></i> <span> Usuarios</span>
						</a>
					</li>
				</ul>
			</li>
			{{-- <li class="treeview">
				<a href="#"><i class="fa fa-group"></i><span>Materia Prima</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					<li class="Materia Prima">
						<a href="{{ route('raw-materials') }}" title="Materia Prima">
							<span>Materia Prima</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="treeview">
				<a href="#"><i class="fa fa fa-shopping-basket"></i><span>Producto</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					<li class="articulo">
						<a href="{{ route('articulo') }}" title="Articulo">
							<i class="fa-solid fa-cart-shopping"></i> <span> Articulo</span>
						</a>
					</li>
                    <li class="provider">
						<a href="{{ route('brand') }}" title="Marca">
							<span>Marca</span>
						</a>
					</li>
					<li class="stage">
						<a href="{{ route('production-stage') }}" title="Etapa">
							<i class="fa-solid fa-cart-shopping"></i> <span> Etapa Produccion</span>
						</a>
					</li>
					<li class="stage">
						<a href="{{ route('production-quality') }}" title="Etapa">
							<i class="fa-solid fa-cart-shopping"></i> <span> Calidad Produccion</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="treeview">
				<a href="#"><i class="fa fa-group"></i><span>Clientes</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					<li class="cliente">
						<a href="{{ route('cliente') }}" title="Cliente">
							<span> Cliente</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="treeview">
				<a href="#"><i class="fa fa-tag"></i><span>Nacionalidad</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					<li class="provider">
						<a href="{{ route('nationalities') }}" title="Marca">
							<span>Nacionalidad</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="treeview">
				<a href="#"><i class="fa fa-group"></i><span>Proveedor</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					<li class="provider">
						<a href="{{ route('provider') }}" title="Proveedor">
							<span> Proveedor</span>
						</a>
					</li>
				</ul>
			</li> --}}
            {{-- <li class="treeview">
				<a href="#"><i class="fa fa-group"></i><span>Compras</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
					<li class="provider">
						<a href="{{ route('wish-purchase') }}" title="Pedido de Compras">
							<span>Pedido de Compras</span>
						</a>
						<a href="{{ route('purchase-order') }}" title="Orden de Compras">
							<span>Orden de Compras</span>
						</a>
						<a href="{{ route('purchase-movement') }}" title="Recepcion de Compras">
							<span>Recepcion de Compras</span>
						</a>
						<a href="{{ route('purchase') }}" title="Factura Compras">
							<span>Factura Compras</span>
						</a>
						<a href="{{ route('inventories') }}" title="Inventario">
							<span>Inventario</span>
						</a>
						<a href="{{ route('reports.stock-product-purchases') }}" title="Existencia">
							<span>Existencia</span>
						</a>
						<a href="{{ route('reports.purchases_report') }}" title="Libro Compra">
							<span>Libro Compra</span>
						</a>
					</li>
				</ul>
			</li> --}}
			<li class="treeview">
				<a href="#"><i class="fa fa-group"></i><span>Servicio</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
				<ul class="treeview-menu">
                    <li class="provider">
						<a href="{{ route('wish_service') }}" title="Pedido de Compras">
							<span>Promociones</span>
						</a>
					</li>
					<li class="provider">
						<a href="{{ route('wish_service') }}" title="Pedido de Compras">
							<span>Pedido de Servicio</span>
						</a>
					</li>
					 <li class="provider">
						<a href="{{ route('budget_service') }}" title="Presupuesto">
							<span>Presupuesto de Servicio</span>
						</a>
					</li>
					<li class="provider">
						<a href="{{ route('contract') }}" title="Contrato">
							<span>Contrato de Servicio</span>
						</a>
					</li>
                    <li class="provider">
						<a href="{{ route('order_service') }}" title="Orden de Servicio">
							<span>Orden de Servicio</span>
						</a>
					</li>
					{{-- <li class="provider">
						<a href="{{ route('production-control-quality') }}" title="Presupuesto">
							<span>Insumos utilizados</span>
						</a>
					</li>
					<li class="provider">
						<a href="{{ route('losses') }}" title="Presupuesto">
							<span>Reclamos del Cliente</span>
						</a>
					</li>
					<li class="provider">
						<a href="{{ route('production-cost') }}" title="Costo Produccion">
							<span>Informes</span>
						</a>
					</li> --}}
				</ul>
			</li>
	</section>
</aside>
